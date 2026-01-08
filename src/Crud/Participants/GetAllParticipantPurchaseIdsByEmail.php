<?php

namespace Eventjuicer\Crud\Participants;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\SortBy;
// use Eventjuicer\Repositories\Criteria\WhereHas;

class GetAllParticipantPurchaseIdsByEmail extends Crud  {

    protected $repo;

    function __construct(ParticipantRepository $repo){
        $this->repo = $repo;
    }

 
    public function get($email = "", $event_id = 0){

        $email = strtolower( trim($email) );
        $event_id = (int) $event_id;

        if(!$email){
            throw new \Exception("email missing...");
        }

        if($event_id){
            $this->repo->pushCriteria(new BelongsToEvent(  $event_id ));
        }

        $this->repo->pushCriteria(new ColumnMatches("email", $email));
        $this->repo->pushCriteria(new SortBy("id", "desc"));
        $this->repo->with(["purchases"]);
        $res = $this->repo->all();

        // Gather all purchases in one iteration
        $allPurchases = $res->pluck("purchases")->collapse();
        
       return $allPurchases->pluck("id")->all();
    }


}


