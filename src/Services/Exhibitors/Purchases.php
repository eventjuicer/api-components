<?php

namespace Eventjuicer\Services\Exhibitors;

use Eventjuicer\Models\Company;
use Eventjuicer\Services\Resolver;

class Purchases {
	
	static protected $skippedRoles = ["representative", "party", "contestant", "contestant_person", "contestant_company"];
    protected $company;

	function __construct(Company $company) {
		$this->company = $company;
	
        $this->company->load("participants.tickets");
    }


	static public function skipRoles(array $skippedRoles){
		self::$skippedRoles = $skippedRoles;
	}

    public function fromEvent(int $eventId = 0) {
       
        return $this->get($eventId);
       
    }

    public function get($eventId = 0) {
       
        if(empty($eventId)){
            $resolver = new Resolver;
            $resolver->fromGroupId($this->company->group_id);
            $eventId = $resolver->getEventId();
        }

        $res = $this->company->participants->filter(function($item) use ($eventId) {
            return $item->event_id == $eventId;
              
        })->pluck("tickets")->collapse()->filter(function($ticket){

            if(in_array($ticket->role, self::$skippedRoles)){
                return false;
            }

            return $ticket->pivot->sold == 1;
        })->values();

        return $res;
       
    }

   
}