<?php 

namespace Eventjuicer\Services;


use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

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



use Uuid;
use Carbon\Carbon;



class SaveOrder {

	use ProvidesConvenienceMethods;


	protected $participant;
	protected $purchase;


	protected $tickets = [];
	protected $fields = [];
	protected $amount = 0;
	protected $currency = "PLN";
	protected $discount = "";
	protected $discount_code_id = 0;


	protected $organizer_id, $group_id, $event_id = 0;

	function __construct(ParticipantRepository $participant, PurchaseRepository $purchase)
	{
		$this->participant = $participant;
		$this->purchase = $purchase;
	}

	function make($event_id = 0, $participant_id = 0, array $tickets, array $fields, $skipValidation = false)
	{
		$this->tickets 	= $tickets;
		$this->fields 	= $fields;


		if(! (int) $event_id && ! (int) $participant_id)
		{
			throw new \Exception();
		}

		$event = Event::find($event_id);

		$this->event_id 		= $event_id;
		$this->group_id 		= $event->group_id;
		$this->organizer_id 	= $event->organizer_id;

		if(! $skipValidation && ! $this->validateFields($fields))
		{
			throw new \Exception("Problem with fields");
		}


		if( ! $this->validateTickets($tickets))
		{
			throw new \Exception("Problem with tickets");
		}

		if(empty($participant_id))
		{
			//create new participant!

			if(empty($fields["email"]))
			{
				throw new \Exception("Email must be provided");
			}	

			$participant = new Participant;

			$participant->event_id 		= $this->event_id;
			$participant->group_id 		= $this->group_id;
			$participant->organizer_id 	= $this->organizer_id;

			$participant->token 		= sha1(Uuid::generate(4));
			$participant->createdon 	= Carbon::now();
			$participant->email 		= $fields["email"];
			$participant->confirmed 	= 1;
			$participant->lang 			= "pl";

			$participant->save();

			$this->saveTickets($participant->id, $tickets);
			
			$this->saveFields($participant->id, $fields);

			$this->participant = $participant;

			//event(new UserWasRegistered());
		}
		
	}



	function getParticipant()
	{
		return $this->participant;
	}

	function saveTickets($participant_id, $tickets)
	{


		//save Purchase

		$purchase = new Purchase();

		
		$purchase->event_id 		= $this->event_id;
		$purchase->group_id 		= $this->group_id;
		$purchase->organizer_id 	= $this->organizer_id;
		$purchase->participant_id 	= $participant_id;
		$purchase->amount 			= 0;
		$purchase->discount 		= 0;
		$purchase->discount_code_id = 0;
		$purchase->paid 			= 1;
		$purchase->status 			= "ok";
		$purchase->status_source 	= "";
		$purchase->createdon		= time();
		$purchase->updatedon		= Carbon::now();
		$purchase->save();


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