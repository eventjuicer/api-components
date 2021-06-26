<?php 

namespace Eventjuicer\Services;

use Illuminate\Http\Request;

//use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\PurchaseRepository;
use Eventjuicer\Repositories\TicketRepository;
use Eventjuicer\Repositories\InputRepository;

use Events\UserWasRegistered;
use Validator;
use Eventjuicer\Models\Participant;
use Eventjuicer\Models\Organizer;
use Eventjuicer\Models\Group;
use Eventjuicer\Models\Event;
use Eventjuicer\Models\PurchaseTicket;
use Eventjuicer\Models\ParticipantFields;
use Eventjuicer\Models\Purchase;
use Eventjuicer\Models\Input;
use Eventjuicer\Models\Ticket;
use Illuminate\Database\Eloquent\Model;

use Uuid;
use Carbon\Carbon;

class SaveOrder {

//	use ProvidesConvenienceMethods;

	protected $request;
	protected $purchase;

	protected $amount = 0;
	protected $currency = "PLN";
	protected $discount = "";
	protected $discount_code_id = 0;
	
	protected $defaultLocale = "pl";
	protected $locale = "pl";

	protected $tickets = [];
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
	protected $validate = true;


	function __construct(Request $request)
	{
		$this->request = $request;
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

	public function setTickets(array $tickets){
		foreach($tickets as $ticket_id => $data){
			$this->tickets[$ticket_id] = $data;
		}
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

	public function clone(){
		
		/*
		
		$fields = (new Personalizer($user))->getProfile();
			
		$fields["email"] = $user->email;


        // $lookup = $this->tickets->getTicketsWithRole("visitor", $this->ActiveEventId() );

        // if($lookup->count() > 1)
        // {
        //     return $this->jsonError("Could not automagically determine ticket!", 500);

        // }

        //SKIP VALIDATION...we will only have fields we got earlier

        $this->order->setEventId(83);
        $this->order->setTickets([1355 => 1]);
        $this->order->setFields($fields);
        $this->order->make();

        return $this->order->getParticipant();

        */
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

	public function skipValidation(bool $skip = true){
		$this->validate = !$skip;
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

		if( !$this->event && !$this->participant )
		{
			throw new \Exception("Either event or participant must be resolved!");
		}

		if( !$this->validate ){

			if( ! $this->validateFields())
			{
			//	throw new \Exception("Problem with fields");
			}

			if( ! $this->validateTickets())
			{
				throw new \Exception("Problem with tickets");
			}		
		}

		if(! $this->participant ){

			//create new participant!
			$this->registerParticipant();			
			//event(new UserWasRegistered());
		}
		
		$this->saveTickets();
			
		$this->saveFields();

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

		if( isset($this->fields["important"]) ){
			$participant->important = intval($this->fields["important"]);
		}

		$participant->save();

		$this->setParticipant($participant);
	}

	public function makeVip(string $referral = ""){

		if(!  $this->participant ){
			throw new \Exception("No participant set!");
		}

		$this->participant->important = 1;
		$this->participant->save();

		$this->updateFields(array(
			"important" => 1,
			"referral" => $referral
		));
	}


	protected function countTotalAmount(){
		
		//count AMOUNT!
		foreach($this->tickets as $ticket_id => $ticket_data){

			if(is_numeric($ticket_data)){
                $quantity = $ticket_data;
            }else{
                $quantity = !empty($ticket_data["quantity"])? $ticket_data["quantity"]: 1;
            }

			$ticket = Ticket::find($ticket_id);

			if(!$ticket){
				throw new \Exception("no ticket found!");
			}

			$localPrice = array_get( $ticket->price, $this->locale);

			if(! is_numeric( $localPrice )) {
				throw new \Exception("no price for this locale!");
			}

			$this->amount += intval($localPrice) * intval($quantity);
		}

		return $this->amount;
	}


	protected function saveTickets(){

		$this->countTotalAmount();

		//save Purchase

		$purchase = new Purchase();
		
		$purchase->event_id 		= $this->event_id;
		$purchase->group_id 		= $this->group_id;
		$purchase->organizer_id 	= $this->organizer_id;
		$purchase->participant_id 	= $this->participant_id;
		$purchase->amount 			= $this->amount;
		$purchase->locale 			= $this->locale;
		$purchase->discount 		= 0;
		$purchase->discount_code_id = 0;
		$purchase->paid 			= $this->amount === 0 ? 1 : 0;
		$purchase->status 			= $this->amount === 0 ? "ok" : "new";
		$purchase->status_source 	= $this->amount === 0 ? "auto" : "manual";
		$purchase->createdon		= time();
		$purchase->updatedon		= Carbon::now();
		$purchase->save();

		$this->setPurchase( $purchase );

		foreach($this->tickets as $ticket_id => $ticket_data){

			if(is_numeric($ticket_data)){
                $quantity = $ticket_data;
            }else{
                $quantity = !empty($ticket_data["quantity"])? $ticket_data["quantity"]: 1;
            }

			$t 					= new PurchaseTicket;
			$t->ticket_id 		= $ticket_id;
			$t->participant_id 	= $this->participant_id;
			$t->purchase_id 	= $purchase->id;
			$t->event_id 		= $this->event_id;
			$t->formdata		= isset($ticket_data["formdata"])? $ticket_data["formdata"]: "";
			$t->quantity 		= $quantity;
			$t->sold 			= 1;
			$t->save();

			unset( $this->tickets[$ticket_id] );
		}
	}

	protected function saveFields()
	{

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


	public function updateFields(array $data = [])
	{

		$this->setFields($data);

		if(! $this->participant ){
			throw new \Exception("No participant defined!");
		}

		foreach($this->fields as $field_name => $field_value)
		{

			$field_name = strtolower(trim($field_name));
			//this is senseless... array should be checked..!
			$field = Input::where("name", $field_name)->first();

			if(is_null($field)) {
				continue;
			}	

			$data = [
					"field_value" 	=> $field_value,
					"updatedon" 	=> Carbon::now(),
					"archive"		=> ""
			];


			$exists = $this->participant->fields()->where("field_id", $field->id)->exists();

			if($exists){

				$this->participant->fields()->updateExistingPivot($field->id, $data);

			}else{


				$data["organizer_id"] = $this->organizer_id;
				$data["group_id"] = $this->group_id;
				$data["event_id"] = $this->event_id;

				$this->participant->fields()->attach($field->id, $data);
			}

		}

		$this->participant = $this->participant->fresh();

	}

	
	protected function validateTickets( )
	{
		foreach($this->tickets as $ticket)
		{
			//check limits....!
		}

		return true;
	}

	protected function validateFields( )
	{
		return Validator::make($this->fields, 
		[
				"fname" 	=> "required", 
				"lname" 	=> "required",
				"email" 	=> "required,email",
				"phone"		=> "required",
				"cname2"	=> "required"
		]);
	}



		

}