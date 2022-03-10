<?php

namespace Eventjuicer\Crud\CompanyPurchasedTickets;

use Eventjuicer\Models\ParticipantTicket;
use Eventjuicer\Crud\Filter;

class FilterActiveEvent extends Filter {
    
    public function filter(ParticipantTicket $item){ 
        
        $activeEvent = $this->activeEventId();

        if($item->event_id != $activeEvent){

            return false;
        }
       
        return true;

    }

}