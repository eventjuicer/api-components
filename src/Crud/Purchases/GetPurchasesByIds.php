<?php

namespace Eventjuicer\Crud\Purchases;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\PurchaseRepository;
// use Eventjuicer\Repositories\ParticipantTicketRepository;
use Eventjuicer\Repositories\Criteria\WhereIn;
// use Eventjuicer\Repositories\Criteria\WhereIn;
// use Eventjuicer\Repositories\Criteria\SortByDesc;

class GetPurchasesByIds extends Crud  {

    protected $repo;

    
    function __construct(PurchaseRepository $repo){
        $this->repo = $repo;
    }

    public function get(){

        $this->setData();

        $ids = $this->getIds();

        $this->repo->pushCriteria(new WhereIn("id", $ids));



    
        // $this->pivot->with([
        //     "purchase.ticketpivot.ticket", 
        //     "purchase.participant", 
        //     "purchase.event"
        // ]);
       
        return $this->repo->all();

    }


  


    

}

