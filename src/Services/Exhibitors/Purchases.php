<?php

namespace Eventjuicer\Services\Exhibitors;

use Eventjuicer\Models\Company;

class Purchases {
	

    protected $company;

	function __construct(Company $company) {
		$this->company = $company;
	}

    public function fromEvent(int $eventId = 0) {
       
        $res = $this->company->participants->filter(function($item) use ($eventId) {
        	return $item->event_id == $eventId;
              
    	})->pluck("tickets")->collapse()->filter(function($ticket){
    		return $ticket->pivot->sold == 1;
    	})->values();

    	return $res;
       
    }


   
}