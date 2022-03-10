<?php

namespace Eventjuicer\Crud\CompanyPurchasedTickets;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\PurchaseRepository;
use Eventjuicer\Repositories\ParticipantTicketRepository;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\WhereIn;
use Eventjuicer\Repositories\Criteria\SortByDesc;


class Fetch extends Crud  {



    protected $pivot;
    
    function __construct(ParticipantTicketRepository $pivot){

        $this->pivot = $pivot;
    }



    public function get(){

        $this->setData();

        $ticket_id = (int) $this->getParam("ticket_id");
        $event_id = (int) $this->getParam("event_id");

        if($ticket_id){
            $this->pivot->pushCriteria(new FlagEquals("ticket_id", $ticket_id));    
        }

        if($event_id){
            $this->pivot->pushCriteria(new BelongsToEvent("event_id", $event_id));    
        }
    
        $this->pivot->with([ 
            "purchase",
            "ticket",
            "participant"
        ]);

        $this->pivot->pushCriteria(new WhereIn("participant_id", $this->getCompanyParticipants() ));
        $this->pivot->pushCriteria(new SortByDesc( "purchase_id" ));
    
        return $this->pivot->all();

    }


  


    

}

