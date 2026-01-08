<?php

namespace Eventjuicer\Crud\Purchases;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\PurchaseRepository;
use Eventjuicer\Crud\Participants\GetAllParticipantPurchaseIdsByEmail;
use Eventjuicer\Repositories\Criteria\WhereIn;

class GetPurchasesByEvent extends Crud  {

    protected $repo;

    function __construct(PurchaseRepository $repo){
        $this->repo = $repo;
    }

    public function byEmail($data = []){

        $repo = clone $this->repo;

        $purchaseIds = app(GetAllParticipantPurchaseIdsByEmail::class)->get($data);       
        $repo->pushCriteria(new WhereIn("id", $purchaseIds));
        $repo->with(["tickets", "participant"]);
        return $repo->all();

    }

    

 
   
    

}

