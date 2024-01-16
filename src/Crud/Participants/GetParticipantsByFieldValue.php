<?php

namespace Eventjuicer\Crud\Participants;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\ParticipantFieldRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\SortBy;
use Eventjuicer\Services\Traits\Fields;

class GetParticipantsByFieldValue extends Crud  {

    use Fields;

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
    
    public function setName($field_name){
        $this->repo->pushCriteria(new FlagEquals("field_id",  $this->getFieldId($field_name)));
        return $this;
    }
    
    public function get($value){
        if(strlen($value)){
            $this->repo->with(["participant.fields"]);
            $this->repo->pushCriteria(new ColumnMatches("field_value",  $value));
            return $this->repo->all()->pluck("participant");
        }

    }
    
}


