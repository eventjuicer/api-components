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
	protected $locksFailed = [];
	protected $newLocksCreated = [];
	protected $locksRemoved = [];


	function __construct(Request $request){
		$this->request = $request;
		
	}
	
	public function setUUID($raw = ""){
		$this->uuid = strlen($raw) != 40 ? sha1($raw): $raw;
	}

	public function getUUID(){
		return $this->uuid;
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

	public function getFailedLocks(){
		return $this->locksFailed;
	}

	public function hasNewLocks(){
		return count($this->newLocksCreated);
	}


	public function sync(){
		$this->removeOldUserLocks(); 
	}

	public function create(){


		/**
		 * purge outdated locks
		 * otherwise we could not set up new lock for next owners
		 */
		$this->removeOldLocks();
		
		foreach($this->tickets as $ticket_id => $data){

			if(empty($data["formdata"]) || empty($data["formdata"]["id"])){
				continue;
			}

			$item_uid = $data["formdata"]["id"];

			if(! $this->setLock($ticket_id, $item_uid, $data["formdata"]) ){
				$this->locksFailed[$ticket_id] = $item_uid;
			}

		}

	}

	public function getLocksForUUID(){

		return PreBooking::where("sessid", $this->uuid)->get();
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

		if(empty($this->uuid)){
			throw new \Exception("no uuid!");
		}

		if(empty($this->event_id)){
			throw new \Exception("no event_id!");
		}

		if(empty($item_uid)){
			throw new \Exception("no item_uid!");
		}

		if(empty($ticket_id) || !is_numeric($ticket_id)){
			throw new \Exception("bad cart data!");
		}

		$lookup = $this->checkLock($ticket_id, $item_uid);

		if($lookup){
			//someone else has a lock - we should report that!
			if($lookup->sessid != $this->uuid){
				return false;
			}
			//there is an active lock but it belongs to same user
			return true;
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

    	if($lock->id){
    		$this->newLocksCreated[] = $lock;
    	}
 
    	return $lock;
	}




	protected function removeOldLocks(){

		$oldLocks = PreBooking::where("blockedon", "<", time() - intval($this->threshold))->get();

		return $oldLocks->each(function($lock){
			$this->locksRemoved[] = $lock;
			$lock->delete();
		});

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

		if($locks->isEmpty()){
			return;
		}

		/**
		 * check if the item is in the cart
		 */

		foreach($locks as $lock){

			if(!isset($this->tickets[$lock->ticket_id])){
				$this->locksRemoved[] = $lock;
				$lock->delete();
				continue;
			}

			$foundLockInCart = false;

			foreach($this->tickets AS $ticket_id => $data){

				if(!isset($data["formdata"]) || !isset($data["formdata"]["id"])){
					continue;
				}

				$item_uid = $data["formdata"]["id"];

				if($ticket_id == $lock->ticket_id && $item_uid == $lock->item_uid){
					$foundLockInCart = true;
				}				
			}

			if(!$foundLockInCart){
				$this->locksRemoved[] = $lock;
				$lock->delete();
			}
		}
	}



}