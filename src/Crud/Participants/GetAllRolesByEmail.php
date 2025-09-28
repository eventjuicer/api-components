<?php

namespace Eventjuicer\Crud\Participants;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\SortBy;
// use Eventjuicer\Repositories\Criteria\WhereHas;

class GetAllRolesByEmail extends Crud  {

    protected $repo;

    function __construct(ParticipantRepository $repo){
        $this->repo = $repo;
    }

 
    public function get($email = ""){

        $email = strtolower( trim($email) );
        $event_id = $this->getParam("event_id");

        if(!$event_id || !$email){
            throw new \Exception("email or event_id missing...");
        }

        $this->repo->pushCriteria(new BelongsToEvent(  $event_id ));
        $this->repo->pushCriteria(new ColumnMatches("email", $email));
        $this->repo->pushCriteria(new SortBy("id", "desc"));
        $this->repo->with(["purchases.tickets"]);
        $res = $this->repo->all();

        //show not cancelled only

        $res = $res->filter(function($participant){
            $with_valid_purchases = $participant->purchases->filter(function($purchase){
                if($purchase->status === "cancelled"){
                    return false;
                }
                return true;
            });
           
            return $with_valid_purchases->count()? true: false;

        });

       return $res->values();
    }


}


