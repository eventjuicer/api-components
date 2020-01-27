<?php

namespace Eventjuicer\Services\Exhibitors;

use Eventjuicer\Models\Company;

class Purchases {
	
	protected $skippedRoles = ["representative", "party", "exhibitor"];
    protected $company;

	function __construct(Company $company) {
		$this->company = $company;
	}

	public function skipRoles(string $skippedRoles){
		$this->skippedRoles = $skippedRoles;
	}

    public function fromEvent(int $eventId = 0) {
       
        $res = $this->company->participants->filter(function($item) use ($eventId) {
        	return $item->event_id == $eventId;
              
    	})->pluck("tickets")->collapse()->filter(function($ticket){

    		if(in_array($ticket->role, $this->skippedRoles)){
    			return false;
    		}

    		return $ticket->pivot->sold == 1;
    	})->values();

    	return $res;
       
    }


   
}