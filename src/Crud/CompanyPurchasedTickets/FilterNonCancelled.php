<?php

namespace Eventjuicer\Crud\CompanyPurchasedTickets;

use Eventjuicer\Models\ParticipantTicket;


class FilterNonCancelled {

    public function filter(ParticipantTicket $item){ 
        
        if(!$item->sold){

            return false;
        }
       
        return true;

    }

}