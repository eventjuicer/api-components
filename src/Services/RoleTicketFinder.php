<?php


namespace Eventjuicer\Services;

 
use Illuminate\Database\Eloquent\Model;
use Eventjuicer\Repositories\EloquentTicketRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\ColumnMatches;

class RoleTicketFinder {
	
	protected $tickets;
	static $eventId;

 	function __construct(EloquentTicketRepository $tickets) {

 		$this->tickets = $tickets;
		
	}

	public static function setEventId($eventId){
		self::$eventId = $eventId;
	}

	public function getOne(string $role){

		$res = $this->get($role);

		if($res->count() > 1){
			throw new \Exception("Only one ticket with role " . $role . " allowed.");
		}

		return $res->first() ? $res->first()->id : 0;
	}

	protected function get(string $role){

		$this->tickets->pushCriteria(new BelongsToEvent(self::$eventId));
		$this->tickets->pushCriteria(new ColumnMatches("role", $role));
		$res = $this->tickets->all();

		if(! $res->count() ){
			throw new \Exception("No ticket found!");
		}

		return $res;
	}


}