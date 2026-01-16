<?php

namespace Eventjuicer\Crud\Purchases;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\PurchaseRepository;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\Criteria\WhereIn;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\SortBy;

class GetPurchasesByEvent extends Crud  {

    protected $email;
    protected $event_id;

    function __construct(){
      
    }

    public function setEmail($email){
        $this->email = strtolower( trim($email) );
        return $this;
    }

    public function setEventId($event_id){
        $this->event_id = intval($event_id);
        return $this;
    }

    public function byEmail(){

        $participantRepo = app(ParticipantRepository::class);
        $purchaseRepo = app(PurchaseRepository::class);

        if(!$this->email || !$this->event_id){
            return [];
        }

        $participantRepo->pushCriteria(new BelongsToEvent(  $this->event_id ));
        $participantRepo->pushCriteria(new ColumnMatches("email", $this->email));
        $participantRepo->pushCriteria(new SortBy("id", "desc"));
        $participantRepo->with(["purchases"]);
        $allPurchasesIds = $participantRepo->repo->all()->pluck("purchases")->collapse()->pluck("id")->all();

        $purchaseRepo->pushCriteria(new WhereIn("id", $allPurchasesIds));
        $purchaseRepo->with(["tickets", "participant"]);
        return $purchaseRepo->all();

    }

    

 
   
    

}

