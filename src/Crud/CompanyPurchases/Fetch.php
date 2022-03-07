<?php

namespace Eventjuicer\Crud\CompanyPurchases;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\PurchaseRepository;
use Eventjuicer\Repositories\ParticipantTicketRepository;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\WhereIn;
use Eventjuicer\Repositories\Criteria\SortByDesc;

class Fetch extends Crud  {

    protected $repo;
    protected $pivot;
    
    function __construct(PurchaseRepository $repo, ParticipantTicketRepository $pivot){
        $this->repo = $repo;
        $this->pivot = $pivot;
    }

    public function get(){

        $this->setData();

        $ticket_id = (int) $this->getParam("ticket_id");

        if($ticket_id){
            $this->pivot->pushCriteria(new FlagEquals("ticket_id", $ticket_id));    
        }else{
    
        }
    
        $this->pivot->with([
            "purchase.ticketpivot.ticket", 
            "purchase.participant", 
            "purchase.event"
        ]);
        $this->pivot->pushCriteria(new WhereIn("participant_id", $this->getCompanyParticipants() ));
        $this->pivot->pushCriteria(new SortByDesc( "purchase_id" ));
    
        return $this->pivot->all()->pluck("purchase");

    }


  


    

}

