<?php namespace Eventjuicer\Services\Search;

use Eventjuicer\Repositories\Repository;


use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Models\ParticipantFields;

class ParticipantSearch {


protected $result;


function __construct(Repository $repo, int $event_id, string $q)
{

        if(!$q || strlen($q) < 3)
        {
            return null;
        }

         $rows = [];

        if(strpos($q, "@")!==false)
        {
            $repo->with(["fields"]);
            $repo->pushCriteria(new BelongsToEvent($event_id));
            $repo->pushCriteria(new ColumnMatches("email", "%" . $q . "%"));
                
            $rows = $repo->all();
        
        }
        else
        {
            if(is_numeric($q) && strlen($q) > 2)
            {

                $rows = ParticipantFields::with("participant", "participant.fields")->where("event_id", $event_id)->where("field_id", 8)->where("field_value", "LIKE", $q."%")->get()->pluck("participant");     

           }
            else
            {

                $rows1 = ParticipantFields::with("participant", "participant.fields")->where("event_id", $event_id)->whereIn("field_id", [3,11])->where("field_value", "LIKE", $q."%")->get()->pluck("participant");


                

                $repo->with(["fields"]);
                $repo->pushCriteria(new BelongsToEvent($event_id));
                $repo->pushCriteria(new ColumnMatches("email", "%" . $q . "%"));
                
                $rows2 = $repo;//->all();

                $rows = $rows1->merge($rows2);
            }
      
        }

        $this->result = $rows->unique("id")->values();

}


function results()
{
    return $this->result;
}




}