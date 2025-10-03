<?php

namespace Eventjuicer\Crud\Purchases;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\PurchaseRepository;
// use Eventjuicer\Repositories\ParticipantTicketRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\SortBy;
use Eventjuicer\Repositories\Criteria\Limit;
use Eventjuicer\Repositories\Criteria\FlagNotEquals;
use Eventjuicer\Repositories\Criteria\FlagEquals;

// use Eventjuicer\Repositories\Criteria\WhereIn;


class GetPurchasesByEvent extends Crud  {

    protected $repo;

    
    function __construct(PurchaseRepository $repo){
        $this->repo = $repo;
    }

    public function query($event_id){


        $repo = clone $this->repo;

        $this->setData();

        $includeFree = $this->getParam("free", 0);
        $status = $this->getParam("status", "all");
      
        $repo->pushCriteria(new BelongsToEvent($event_id));
       
        $repo->pushCriteria(
            new SortBy($this->getParam("_sort", "id"), $this->getParam("_order", "DESC")));
       
        if(!$includeFree){
            $repo->pushCriteria(new FlagNotEquals("amount", 0));
        }
        if($status != "all"){
            $repo->pushCriteria(new FlagEquals("status", $status));
        }

        return $repo;

    }

    public function getPaginated($event_id){

        $take = $this->getParam("_end", 25) - $this->getParam("_start", 0);

        $repo  = $this->query($event_id);
        
        $repo->with(["tickets", "participant"]);
        $repo->pushCriteria(
            new Limit($take, $this->getParam("_start", 0))
        );

        return $repo->all();
    }


    public function getAll($event_id){
        $repo  =$this->query($event_id);
        $repo->with(["tickets"]);
        return $repo->all();
    }

    public function getCount($event_id){
        $repo  = $this->query($event_id); 
        return $repo->all()->count();
    }

    

}

