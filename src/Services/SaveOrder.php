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



use Uuid;
use Carbon\Carbon;



class SaveOrder {

//	use ProvidesConvenienceMethods;

	protected $request;
	protected $participant;
	protected $purchase;


	protected $tickets = [];
	protected $fields = [];
	protected $amount = 0;
	protected $currency = "PLN";
	protected $discount = "";
	protected $discount_code_id = 0;

	protected $defaultLocale = "pl";
	protected $locale = "pl";

	protected $organizer_id, $group_id, $event_id = 0;


	function __construct(Request $request)
	{
		$this->request = $request;
	}

	function setLocale(string $locale){

		$this->locale = $locale;
	}

	function getParticipant()
	{
		return $this->participant;
	}

	function getPurchase()
	{
		return $this->purchase;
	}


	function configure($event_id){


		$event = Event::find($event_id);

		$this->event_id 		= $event_id;
		$this->group_id 		= $event->group_id;
		$this->organizer_id 	= $event->organizer_id;

	}


	function make(
							$event_id = 0, 
							$participant_id = 0, 
							array $tickets, 
							array $fields, 
							$skipValidation = false, 
							$parent_id = 0
	) {

		$this->tickets 	= $tickets;
		$this->fields 	= $fields;


		if(! (int) $event_id && ! (int) $participant_id)
		{
			throw new \Exception("Either event id or participant id must be given!");
		}



		$this->configure( $event_id );

		

		if( ! $skipValidation )
		{

			if( ! $this->validateFields($fields))
			{
				throw new \Exception("Problem with fields");
			}


			if( ! $this->validateTickets($tickets))
			{
				throw new \Exception("Problem with tickets");
			}
		
		}


		if(! intval($participant_id) > 0)
		{
			//create new participant!

			if(empty($fields["email"]) || strpos($fields["email"], "@")===false)
			{
				throw new \Exception("Valid email must be provided");
			}	

			$participant = new Participant;

			$participant->event_id 		= intval($this->event_id);
			$participant->group_id 		= intval($this->group_id);
			$participant->organizer_id 	= intval($this->organizer_id);
			$participant->parent_id 	= intval($parent_id);

			$participant->company_id 	= $parent_id ? Participant::find($parent_id)->company_id : 0;
			

			$participant->token 		= sha1(Uuid::generate(4));
			$participant->createdon 	= Carbon::now();
			$participant->email 		= $fields["email"];
			$participant->confirmed 	= 1;
			$participant->lang 			= $this->locale;

			$participant->save();

			$this->participant = $participant;

			//event(new UserWasRegistered());
		}
		else
		{
			$this->participant = Participant::find($participant_id);
		}

		
		$this->saveTickets($this->participant->id, $tickets);
			
		$this->saveFields($this->participant->id, $fields);


	}




	function saveTickets($participant_id, $tickets)
	{

		//count AMOUNT!

		foreach($tickets as $ticket_id => $quantity){

			$ticket = Ticket::find($ticket_id);

			if(!$ticket){
				throw new \Exception("no ticket found!");
			}

			$localeAmount = intval( array_get($ticket->price, $this->locale, $ticket->price["pl"]) );

			$this->amount += $localeAmount * $quantity;
		}

		//save Purchase

		$purchase = new Purchase();

		
		$purchase->event_id 		= $this->event_id;
		$purchase->group_id 		= $this->group_id;
		$purchase->organizer_id 	= $this->organizer_id;
		$purchase->participant_id 	= $participant_id;
		$purchase->amount 			= $this->amount;
		$purchase->discount 		= 0;
		$purchase->discount_code_id = 0;
		$purchase->paid 			= $this->amount === 0 ? 1 : 0;
		$purchase->status 			= $this->amount === 0 ? "ok" : "new";
		$purchase->status_source 	= $this->amount === 0 ? "auto" : "manual";
		$purchase->createdon		= time();
		$purchase->updatedon		= Carbon::now();
		$purchase->save();

		$this->purchase = $purchase;

		foreach($tickets as $ticket_id => $quantity)
		{	
			$t 					= new PurchaseTicket;
			$t->ticket_id 		= $ticket_id;
			$t->participant_id 	= $participant_id;
			$t->purchase_id 	= $purchase->id;
			$t->event_id 		= $this->event_id;
			$t->formdata		= "";
			$t->quantity 		= $quantity;
			$t->sold 			= 1;
			$t->save();
		}
	}

	protected function saveFields($participant_id, $fields)
	{

		foreach($fields as $field_name => $field_value)
		{

			//this is senseless... array should be checked..!

			$input = Input::where("name", $field_name)->first();

			$field_id = $input ? $input->id : 0;

			if(empty($field_id))
			{
				continue;
			}

			$pf = new ParticipantFields;
			$pf->participant_id = $participant_id;
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


	public function updateFields($participant_id, array $fields = [])
	{

		$user = Participant::find( $participant_id );

		if(empty($user->id)){
			throw new \Exception("No such user!");
		}

		foreach($fields as $field_name => $field_value)
		{

			//this is senseless... array should be checked..!

			$input = Input::where("name", $field_name)->first();

			$field_id = $input ? $input->id : 0;

			if(empty($field_id)) {

				continue;
			}	

			$user->fields()->updateExistingPivot($field_id, [
				"field_value" 	=> $field_value,
				"updatedon" 	=> Carbon::now()
			]);

		}

	}

	
	protected function validateTickets(array $tickets)
	{
		foreach($this->tickets as $ticket)
		{
			//check limits....!
		}

		return true;
	}

	protected function validateFields(array $fields)
	{
		return Validator::make($fields, 
		[
				"fname" 	=> "required", 
				"lname" 	=> "required",
				"email" 	=> "required,email",
				"phone"		=> "required",
				"cname2"	=> "required"
		]);
	}



		

}