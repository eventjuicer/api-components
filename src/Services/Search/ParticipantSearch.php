<?php namespace Eventjuicer\Services\Search;

//use Eventjuicer\Repositories\Repository;

use  Bosnadev\Repositories\Eloquent\Repository;

use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Models\ParticipantFields;

class ParticipantSearch {


protected $result;


function __construct(Repository $repo, int $event_id, string $q, $addFields = []){

        if(!$q || strlen($q) < 3){
            return;
        }


        if(strpos($q, "@")!==false){

            $repo->with(["fields"]);
            $repo->pushCriteria(new BelongsToEvent($event_id));
            $repo->pushCriteria(new ColumnMatches("email", "%" . $q . "%"));
                
            $this->result = $repo->all();
        
        }else{

            if(is_numeric($q) && strlen($q) > 2){

                $rows = ParticipantFields::with("participant", "participant.fields")->where("event_id", $event_id)->where("field_id", 8)->where("field_value", "LIKE", $q."%")->get()->pluck("participant");  

                $this->result = $rows;   

           }else{

                $rows1 = ParticipantFields::with("participant", "participant.fields")->where("event_id", $event_id)->whereIn("field_id", array_merge([3,11], (array) $addFields))->where("field_value", "LIKE", $q."%")->get()->pluck("participant");

                $repo->with(["fields"]);
                $repo->pushCriteria(new BelongsToEvent($event_id));
                $repo->pushCriteria(new ColumnMatches("email", "%" . $q . "%"));    
                $rows = $rows1->merge( $repo->all() );

                $this->result = $rows->unique("id")->values();

            }
        }
}


function results()
{
    return $this->result ? $this->result : collect([]);
}




}