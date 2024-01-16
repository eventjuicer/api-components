<?php

namespace Eventjuicer\Services\Tickets;

use Eventjuicer\Crud\Tickets\GetTicketsByIds;

class CheckifAnyPaidTickets {

    private $tickets;

    function __construct(GetTicketsByIds $tickets)
    {
        $this->tickets = $tickets;
    }

    public function check(array $ids){
        
        $tickets = $this->tickets->get($ids);

        return $tickets->filter(function($ticket){

            //array_sum(array_values($price)) ? 1 : 0;

            if($ticket->paid){
                return true;
            }
            return false;

        })->count() > 0;

    }

}