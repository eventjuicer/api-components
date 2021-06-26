<?php 

namespace Eventjuicer\Services;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Uuid;
use Carbon\Carbon;
use Eventjuicer\Models\PreBooking;
use Eventjuicer\Models\ParticipantTicket;


class SavePaidOrder {

	protected $request;
	protected $threshold = 600;
	protected $event_id;
	protected $sessid;


	function __construct(Request $request){
		$this->request = $request;
		
	}
	
	function createSession(){
		$this->setSession(sha1(Uuid::generate(4)));
	}

	function setSession($sessid = ""){
		$this->sessid = $sessid;
	}

	function setEventId($event_id){
		$this->event_id = (int) $event_id;
	}

	function lookupPurchases(){

		ParticipantTicket::where()

	}

	function createLocksForPotentialPurchase($data = []){
		

		// tickets[1808]: 1
		// ticketdata[1808]: {"ti":"A9.1","id":"booth-126-336"}

		//before locking we must remove old locks associated with this sessid

		if(!empty($data["sessid"])){

			$this->setSession($data["sessid"]);
			$this->removeLockForSession();
		}

		$lockStatus = true;

		foreach($data["tickets"] as $ticket_id => $quantity){

			if(!$quantity>0){
				continue;
			}

			$ticketdata = !empty($data["ticketdata"]) && isset($data["ticketdata"][$ticket_id])? json_decode(stripslashes($data["ticketdata"][$ticket_id]), true): [];

			//before setting-up a new lock we must ensure there is no lock is

			$lock = PreBooking::where("ticket_id", $ticket_id)->whereNot("sessid", $this->sessid)->first();

			$this->createLock($ticket_id, $ticketdata);
		}

		return $lockStatus;
	}

	function createLock($ticket_id= 0, $ticketdata= ""){

		//validate ticket_id

    	$participant->createdon 	= Carbon::now();

    	//check if not already present?

    	//check if not already purchased?

    	$lock = new PreBooking;
    	$lock->sessid = $this->sessid;
    	$lock->ticket_id = $ticket_id;
    	$lock->ticketdata = $ticketdata;
    	$lock->event_id = $this->event_id;
    	$lock->blockedon = time();
    	$lock->ip = $this->request->ip();


	}		


	function removeOldLocks(){
		PreBooking::where("blockedon", "<", time() - intval($this->threshold))->delete();
	}

	function removeLockForSession(){
		PreBooking::like("sessid", $this->sessid)->delete();
	}

}