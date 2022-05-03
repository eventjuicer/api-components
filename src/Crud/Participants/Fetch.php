<?php

namespace Eventjuicer\Crud\Participants;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\SortBy;
use Eventjuicer\Repositories\Criteria\WhereHas;

class Fetch extends Crud  {

    protected $repo;

    function __construct(ParticipantRepository $repo){
        $this->repo = $repo;
    }

    public function getByEventId($event_id){
        $this->repo->pushCriteria(new BelongsToEvent( (int) $event_id ));
        $res = $this->repo->all();
        return $res;
    }

    public function countVisitorsByEventId($event_id){

        $this->repo->pushCriteria(new BelongsToEvent(  $event_id ));
        $this->repo->pushCriteria(new WhereHas("purchases.tickets", function($q){
            $q->where("role", "visitor");
        }));
        $res = $this->repo->all(["id"]);
       return $res->count();
    }
 
    public function getVisitorsByEmail($email = ""){

        $event_id = $this->getContextFromHost()->getEventId();

        if(!$event_id || !$email){
            return collect([]);
        }

        $this->repo->pushCriteria(new BelongsToEvent(  $event_id ));
        $this->repo->pushCriteria(new ColumnMatches("email", strtolower( trim($email)) ));
        $this->repo->pushCriteria(new WhereHas("purchases.tickets", function($q){
            $q->where("role", "visitor");
        }));
        $this->repo->pushCriteria(new SortBy("id", "DESC"));
        $this->repo->with(["fields","purchases.tickets"]);
        $res = $this->repo->all();
       return $res;
    }


}


