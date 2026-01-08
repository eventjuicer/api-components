<?php 

namespace Eventjuicer\Services;

use Illuminate\Http\Request;

 

use Events\UserWasRegistered;
use Validator;
use Eventjuicer\Models\Participant;
use Eventjuicer\Models\Event;
use Eventjuicer\Models\ParticipantTicket;
use Eventjuicer\Models\ParticipantFields;
use Eventjuicer\Models\Purchase;
use Eventjuicer\Models\Input;
use Eventjuicer\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use Eventjuicer\Crud\CompanyMeetups\CreateByParticipant as CreateMeetupWithCompany;
use Eventjuicer\Services\Vipcodes\VipFromVisitorRegistration;
use Eventjuicer\Jobs\RunSyncWithSecondaryDatabaseJob;
use Eventjuicer\Services\TicketsSold;

use Uuid;
use Carbon\Carbon;

/** VIPS */


class SaveNewOrder {

//	use ProvidesConvenienceMethods;

	protected $request;
	protected $ticketssold;
	protected $vipcodeHandler;
	protected $meetupHandler;
	protected $purchase;
	protected $amount = 0;
	protected $currency = "PLN";
	protected $discount = "";
	protected $discount_code_id = 0;
	protected $defaultLocale = "pl";
	protected $locale = "pl";
	protected $cartItems = [];
	protected $fields = [];
	protected $parent_id = 0;
	protected $parent = null;
	protected $company_id = 0;
	protected $participant_id = 0;
	protected $participant = null;
	protected $event = null;
	protected $organizer_id = 0;
	protected $group_id = 0;
	protected $event_id = 0;
	protected $errors = [];
	protected $validateTickets = true;

	function __construct(
		Request $request, 
		TicketsSold $ticketssold, 
		VipFromVisitorRegistration $vipcodeHandler, 
		CreateMeetupWithCompany $meetupHandler
	){
		$this->request = $request;
		$this->ticketssold = $ticketssold;
		$this->vipcodeHandler = $vipcodeHandler;
		$this->meetupHandler = $meetupHandler;
	}

	/* SETTERS */

	public function setLocale(string $locale){

		if(strlen($locale) === 2){
			$this->locale = strtolower($locale);
		}
	}

	public function setFields(array $fields){
		foreach($fields as $field_name => $field_value){
			$this->fields[$field_name] 	= $field_value;
		}
	}

	public function setCartItems(array $cartItems){
		$this->cartItems = $cartItems;
	}

	// public function addTickets(array $tickets){
	// 	foreach($tickets as $ticket_id => $data){
	// 		$this->tickets[$ticket_id] = $data;
	// 	}
	// }

	public function setValidateTickets(bool $setting){
		$this->validateTickets = $setting;
	}

	public function setParticipantId(int $participant_id){

		if($participant_id){
			$this->setParticipant(Participant::findOrFail($participant_id));
		}
	}

	public function setParticipantByToken(string $token){

		if($token){
			$this->setParticipant( Participant::where("token", $token)->first() );
		}
	}

 
	public function setParticipant(Model $model){
		
		$this->participant = $model;
		$this->participant_id = $model->id;

		if(! $this->event ){
			$this->setEventId($model->event_id);
		}
	}

	public function setParentId(int $parent_id){
		
		if($parent_id){
			$this->setParent( Participant::findOrFail($parent_id) );
		}
	}

	public function setParent(Model $model){
		$this->parent = $model;
		$this->parent_id = $model->id;
		$this->company_id = $model->company_id;

	}

	public function setEventId(int $event_id){
		$this->setEvent($event_id);
	}

	public function setEvent(int $event_id){
		//generally it should be taken from tickets! :)

		if($event_id){
			
			$event = Event::findOrFail($event_id);
			$this->event 			= $event;
			$this->event_id 		= $event->id;
			$this->group_id 		= $event->group_id;
			$this->organizer_id 	= $event->organizer_id;
		}
	}

	/* GETTERS */

	public function getParticipant()
	{
		return $this->participant;
	}

	public function getParticipantId()
	{
		return $this->participant->id;
	}


	public function getPurchase()
	{
		return $this->purchase;
	}

	public function setPurchase(Purchase $purchase){
		$this->purchase = $purchase;
	}

	function make(){

 
		if( !$this->event && !$this->participant ){
			throw new \Exception("Either event or participant must be resolved!");
		}

	
		if(!empty($this->errors)){
			throw new \Exception(implode(",", $this->errors));
		}

		if(! $this->participant ){

			//create new participant!
			$this->registerParticipant();	
			$this->saveFields();		
			//event(new UserWasRegistered());
		}
		
		$this->saveCartItems();
			
 
		dispatch( new RunSyncWithSecondaryDatabaseJob( $this->participant->id, $this->participant->organizer_id ) );

	}

	public function registerParticipant(){

		if(empty($this->fields["email"]) || strpos($this->fields["email"], "@")===false){
				throw new \Exception("Valid email must be provided");
		}	

		if( ! $this->event){

			throw new \Exception("No event data!");
		}

		$participant = new Participant;

		$participant->event_id 		= $this->event_id;
		$participant->group_id 		= $this->group_id;
		$participant->organizer_id 	= $this->organizer_id;
		$participant->parent_id 	= $this->parent_id;
		$participant->company_id 	= $this->company_id;

		$participant->token 		= sha1(Uuid::generate(4));
		$participant->createdon 	= Carbon::now();
		$participant->email 		= $this->fields["email"];
		$participant->confirmed 	= 1;
		$participant->lang 			= $this->locale;

		/** legacy! */
		if( isset($this->fields["important"]) ){
			$participant->important = intval($this->fields["important"]);
		}

		if( isset($this->fields["unsubscribed"]) ){
			$participant->unsubscribed = intval($this->fields["unsubscribed"]);
			unset($this->fields["unsubscribed"]);
		}

		$participant->save();

		$this->setParticipant($participant);

		
	}

	protected function countTotalAmount(){

		//count AMOUNT!
		foreach($this->cartItems as $cartItem){

			$quantity = !empty($cartItem["quantity"]) ? $cartItem["quantity"] : 1;
			$ticket_id = $cartItem["id"];
			$currency = $cartItem["currency"];

			$ticket = Ticket::find($ticket_id);

			if(!$ticket){
				throw new \Exception("no ticket found!");
			}

			$localPrice = array_get( $ticket->price, $currency==="EUR" ? "en" : "pl");

			if(! is_numeric( $localPrice )) {
				throw new \Exception("no price for this currency!");
			}

			$this->amount += intval($localPrice) * intval($quantity);
		}

		return $this->amount;
	}

	public function savePurchase($free = false){

		$this->countTotalAmount();

		if(empty($this->event_id) || empty($this->group_id) || empty($this->organizer_id) || empty($this->participant_id)){
			throw new \Exception("Not enough data to save purchase!");
		}

		$purchase = new Purchase();
		
		$purchase->event_id 		= $this->event_id;
		$purchase->group_id 		= $this->group_id;
		$purchase->organizer_id 	= $this->organizer_id;
		$purchase->participant_id 	= $this->participant_id;
		$purchase->amount 			= $this->amount;
		$purchase->locale 			= intval($this->organizer_id) === 5 ? "en" : $this->locale;
		$purchase->discount 		= $free? $this->amount : 0;
		$purchase->discount_code_id = 0;
		$purchase->paid 			= $free || $this->amount === 0 ? 1 : 0;
		$purchase->status 			= $free || $this->amount === 0 ? "ok" : "new";
		$purchase->status_source 	= $free || $this->amount === 0 ? "auto" : "manual";
		$purchase->createdon		= time();
		$purchase->updatedon		= Carbon::now();
		$purchase->save();

		$this->setPurchase( $purchase );
	}

	public function saveTicket(array $cartItem){

		if(empty($this->purchase) || empty($this->purchase->id) || empty($this->participant_id)){
			throw new \Exception("Purchase not saved!");
		}

		$quantity = !empty($cartItem["quantity"]) ? $cartItem["quantity"] : 1;
		$ticket_id = $cartItem["id"];

		$t 					= new ParticipantTicket;
		$t->ticket_id 		= $ticket_id;
		$t->participant_id 	= $this->participant_id;
		$t->purchase_id 	= $this->purchase->id;
		$t->event_id 		= $this->event_id;
		$t->formdata		= isset($cartItem["metadata"])? $cartItem["metadata"]: "";
		$t->quantity 		= $quantity;
		$t->sold 			= 1;
		$t->save();

		return $t;

	}

	protected function saveCartItems(){

		

		//save Purchase

		$this->savePurchase();

		foreach($this->cartItems as $index => $cartItem){

			$ticket = $this->saveTicket($cartItem);
     
			if($ticket->id){
				unset( $this->cartItems[$index] );
			}
		}
	}

	public function saveFields(){

		if(empty($this->participant_id) || empty($this->event_id) || empty($this->group_id) || empty($this->organizer_id)){
			throw new \Exception("Not enough data to save fields!");
		}

		foreach($this->fields as $field_name => $field_value)
		{

			//this is senseless... array should be checked..!

			$input = Input::where("name", $field_name)->first();

			$field_id = $input ? $input->id : 0;

			if(empty($field_id))
			{
				continue;
			}

			$pf = new ParticipantFields;
			$pf->participant_id = $this->participant_id;
			$pf->organizer_id 	= $this->organizer_id;
			$pf->group_id 		= $this->group_id;
			$pf->event_id 		= $this->event_id;
			$pf->archive 		= "";
			$pf->updatedon  	= Carbon::now();
			$pf->field_id 		= $field_id;
			$pf->field_value 	= $field_value;
			$saved = $pf->save();

			unset( $this->fields[$field_name] );
			
		}
	}


	



}