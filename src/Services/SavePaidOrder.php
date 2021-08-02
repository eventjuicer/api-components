<?php 

namespace Eventjuicer\Services;

use Eventjuicer\Contracts\SavesPaidOrder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Uuid;
use Carbon\Carbon;
use Eventjuicer\Models\PreBooking;
use Eventjuicer\Models\ParticipantTicket;
use Arr;

class SavePaidOrder implements SavesPaidOrder {

	protected $request;
	protected $threshold = 600;
	protected $event_id = 0;
	protected $uuid = "";
	protected $tickets = [];


	function __construct(Request $request){
		$this->request = $request;
		
	}
	
	public function setUUID($raw = ""){

		$this->uuid = strlen($raw) > 40 ? sha1($raw): $raw;
	}

	public function setEventId($event_id){
		if(is_numeric($event_id)){
			$this->event_id = (int) $event_id;
		}
	}

	public function setTickets(array $tickets){
		$this->tickets = $tickets;
	}

	public function setThreshold($val){
		if(is_numeric($val)){
			$this->threshold = (int) $val;
		}
	}

	public function getThreshold(){
		return $this->threshold;
	}

	public function getCurrentLocks(){

		if(!$this->event_id || !is_numeric($this->event_id)){
			throw new \Exception("setEventId() missing");
		}

		//purge old items?
        $this->removeOldLocks();
		
		return PreBooking::where("event_id", $this->event_id)->where("blockedon", ">", time() - intval($this->threshold))->get();

	}

	public function create(){

		/**
		 * Scenario 1
		 * we add new items to the cart...
		 */

		foreach($this->tickets as $ticket_id => $data){

			if(empty($data["formdata"]) || empty($data["formdata"]["id"])){
				continue;
			}

			$item_uid = $data["formdata"]["id"];

			$this->setLock($ticket_id, $item_uid, $data["formdata"]);
		}

		/**
		 * Scenario 2
		 * a) cart emptied
		 * b) cart item removed
		 * = we should remove items that are not present in the cart
		 */

		/**
		 * + purge outdated locks
		 */
		$this->removeOldLocks();
		$this->removeOldUserLocks(); 
		return $this->getLocksForUUID();
	}		


	public function filterTickets(){

		//check locks against tickets
		$locks = $this->getLocksForUUID();

		foreach($this->tickets as $ticket_id => $data){

			/*
			* leave ticket as is
			*/
			if(empty($data["formdata"]) || empty($data["formdata"]["id"])){
				continue;
			}

			$item_uid = $data["formdata"]["id"];

			if($locks->isEmpty()){
				unset($this->tickets[$ticket_id]);
			}

			//check if lock exists

			if(!$locks->firstWhere("item_uid", $item_uid)){
				unset($this->tickets[$ticket_id]);
			}
		
		}

		return $this->tickets;
	}


	/**
	 * we don't care about the owner here...
	 * */
	protected function checkLock($ticket_id, $item_uid){

		//{quantity: 1, formdata: {ti: "G10", id: "booth-0-918"}}}

		return PreBooking::where("ticket_id", $ticket_id)->where("item_uid", $item_uid)->first();

	}

	protected function setLock($ticket_id, $item_uid, $ticketdata){

		if(!$this->uuid || strlen($this->uuid)!=40){
			return null;
		}

		if(!$ticket_id || !is_numeric($ticket_id)){
			return null;
		}

		if($this->checkLock($ticket_id, $item_uid)){
			//already set...lets keep it!
			return false;
		}

		$lock = new PreBooking;
    	$lock->sessid = $this->uuid;
    	$lock->ticket_id = $ticket_id;
    	$lock->item_uid = $item_uid;
    	/**
    	 * compat
    	 */
    	$lock->ticketdata = $ticketdata;
    	/**
    	 * compat
    	 */
    	$lock->event_id = $this->event_id;
    	$lock->blockedon = time();
    	$lock->ip = $this->request->ip();
    	$lock->save();

    	return $lock;
	}

	protected function getLocksForUUID(){

		return PreBooking::where("sessid", $this->uuid)->where("event_id", $this->event_id)->get();
	}


	protected function removeOldLocks(){

		/**
		 * purge old 
		 * */
		return PreBooking::where("blockedon", "<", time() - intval($this->threshold))->delete();

	}

	/**
	 * 
	 * remove locks that were previously set for the users but are not currently present in their cart
	 * (1) compare tickets - faster
	 * (2) if tickets match we must still compare formdata
	 * 
	 * */
	protected function removeOldUserLocks(){

		$locks = $this->getLocksForUUID();

		/**
		 * check if the item is in the cart
		 */

		foreach($locks as $lock){

			if(!isset($this->tickets[$lock->ticket_id])){
				$lock->delete();
				continue;
			}

			$found = false;

			foreach($this->tickets AS $ticket_id => $data){

				$item_uid = $data["formdata"]["id"];
				if($ticket_id == $lock->ticket_id && $item_uid == $lock->item_uid){
					$found = true;
				}				
			}

			if(!$found){
				$lock->delete();
			}
		}
	}



}