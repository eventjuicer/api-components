<?php

namespace Eventjuicer\Crud\CompanyPurchasedTickets;

use Eventjuicer\Models\ParticipantTicket;

class FilterArrangement {

    public function filter(ParticipantTicket $item){ 
        
        if( $item->ticket->role == "service_external"){
            return true;
        }

        return false;


    }

}