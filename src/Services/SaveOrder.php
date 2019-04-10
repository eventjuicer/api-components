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
			$this->locale = $locale;
		}
	}

	public function setFields(array $fields){
		$this->fields 	= $fields;
	}

	public function setTickets(array $tickets){
		$this->tickets 	= $tickets;
	}

	public function setParticipantId(int $participant_id){

		$participant = Participant::find($participant_id);

		if($participant_id > 0 && $participant){
			$this->participant_id = $participant_id;
			$this->participant = $participant;
		}
	}

	public function setParticipant(Model $model){
		$this->participant_id = $model->id;
		$this->participant = $model;
	}

	public function setParentId(int $parent_id){
		
		$parent = Participant::find($parent_id);

		if($parent_id > 0 && $parent){

			$this->parent_id = $parent_id;

			if($parent->company_id){
				$this->company_id = $parent->company_id;
			}
		}
	}

	public function setParent(Model $model){
		$this->parent_id = $model->id;
		$this->parent = $model;

		if($model->company_id){
			$this->company_id = $model->company_id;
		}
	}

	public function skipValidation(bool $skip = true){
		$this->validate = !$skip;
	}

	public function setEvent(int $event_id){
		//generally it should be taken from tickets! :)

		$event = Event::find($event_id);

		if($event_id > 0 && $event){
			$this->event_id 		= $event_id;
			$this->group_id 		= $event->group_id;
			$this->organizer_id 	= $event->organizer_id;
		}

	}

	/* GETTERS */

	function getParticipant()
	{
		return $this->participant;
	}

	function getPurchase()
	{
		return $this->purchase;
	}

	function make(
							$event_id = 0, 
							$participant_id = 0, 
							array $tickets, 
							array $fields, 
							$skipValidation = false, 
							$parent_id = 0,
							$locale = ""
	) {


		if(! (int) $event_id && ! (int) $participant_id)
		{
			throw new \Exception("Either event id or participant id must be given!");
		}


		$this->setLocale($locale);
		$this->setTickets($tickets);
		$this->setFields($fields);
		$this->setEvent($event_id);
		$this->setParentId($parent_id);
		$this->setParticipantId($participant_id);
		$this->skipValidation($skipValidation);



		if( ! $this->validate )
		{

			if( ! $this->validateFields())
			{
				throw new \Exception("Problem with fields");
			}


			if( ! $this->validateTickets())
			{
				throw new \Exception("Problem with tickets");
			}
		
		}


		if(! $this->participant_id )
		{
			//create new participant!

			if(empty($this->fields["email"]) || strpos($this->fields["email"], "@")===false)
			{
				throw new \Exception("Valid email must be provided");
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

			$participant->save();

			$this->setParticipant($participant);

			//event(new UserWasRegistered());
		}
		
		$this->saveTickets();
			
		$this->saveFields();

	}


	protected function saveTickets()
	{

		//count AMOUNT!

		foreach($this->tickets as $ticket_id => $quantity){

			$ticket = Ticket::find($ticket_id);

			if(!$ticket){
				throw new \Exception("no ticket found!");
			}

			$localPrice = array_get( $ticket->price, $this->locale);

			if(! is_numeric( $localPrice )) {
				throw new \Exception("no price for this locale!");
			}

			$this->amount += intval($localPrice) * $quantity;
		}

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

		$this->purchase = $purchase;

		foreach($this->tickets as $ticket_id => $quantity)
		{	
			$t 					= new PurchaseTicket;
			$t->ticket_id 		= $ticket_id;
			$t->participant_id 	= $this->participant_id;
			$t->purchase_id 	= $purchase->id;
			$t->event_id 		= $this->event_id;
			$t->formdata		= "";
			$t->quantity 		= $quantity;
			$t->sold 			= 1;
			$t->save();
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
			$pf->save();

		}

	}


	public function updateFields($data = [])
	{

		if(!empty($data) && is_array($data)){
			$this->setFields($data);
		}

		if(empty($this->participant_id)){
			throw new \Exception("No participant defined!");
		}

		foreach($this->fields as $field_name => $field_value)
		{

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