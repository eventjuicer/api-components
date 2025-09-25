<?php

namespace Eventjuicer\Crud\Purchases;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\PurchaseRepository;
// use Eventjuicer\Repositories\ParticipantTicketRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\SortByDesc;
// use Eventjuicer\Repositories\Criteria\WhereIn;


class GetPurchasesByEvent extends Crud  {

    protected $repo;

    
    function __construct(PurchaseRepository $repo){
        $this->repo = $repo;
    }

    public function get($event_id){

        $this->setData();

        $this->repo->pushCriteria(new BelongsToEvent($event_id));

        $this->repo->with(["participant"]);

        $this->repo->pushCriteria(new SortByDesc("id"));
    
        // $this->pivot->with([
        //     "purchase.ticketpivot.ticket", 
        //     "purchase.participant", 
        //     "purchase.event"
        // ]);
       
        return $this->repo->all();

    }


  


    

}

