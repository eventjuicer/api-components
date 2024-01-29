<?php

namespace Eventjuicer\Crud\Participants;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\ParticipantFieldRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\ProfileFieldMatches;
use Eventjuicer\Repositories\Criteria\SortBy;

class GetParticipantsByFieldValue extends Crud  {


    protected $repo;

    function __construct(ParticipantFieldRepository $repo){
        $this->repo = $repo;
    }

    public function setEventId($event_id){
        if(is_numeric($event_id) && $event_id > 0){
            $this->repo->pushCriteria(new BelongsToEvent($event_id));
        }
        return $this;
    }
    
    public function setConditions(array $conditions){
        $this->repo->pushCriteria(new ProfileFieldMatches($conditions));    
        return $this;
    }
    
    public function get($with=["participant"]){

        $this->repo->with($with);
        return $this->repo->all()->pluck("participant");
        
    }
    
}


