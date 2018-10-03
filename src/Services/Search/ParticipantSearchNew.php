<?php 

namespace Eventjuicer\Services\Search;

//use Eventjuicer\Repositories\Repository;

use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;

use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Models\ParticipantFields;

class ParticipantSearchNew {


protected $participants;


function __construct(ParticipantRepository $participants)
{
       $this->participants = $participants;
}


function group($group_id, $q, $addFields = [])
{

        $rows = [];

        if(strpos($q, "@")!==false)
        {
            return $this->searchByEmail($group_id, $q);
        }
        else
        {
            if(is_numeric($q) && strlen($q) > 2)
            {

                return ParticipantFields::where("group_id", $group_id)->where("field_id", 8)->where("field_value", "LIKE", $q."%")->get()->pluck("participant_id")->unique()->all();

           }
            else
            {

                $rows1 = ParticipantFields::where("group_id", $group_id)->whereIn("field_id", array_merge([3,11], (array) $addFields))->where("field_value", "LIKE", $q."%")->get()->pluck("participant_id")->unique()->all();

                $rows2 = $this->searchByEmail($group_id, $q);

                return array_unique (array_merge ($rows1, $rows2));

            }           
      
        }
        
        
}

    protected function searchByEmail($group_id, $q){

            $this->participants->pushCriteria(new BelongsToGroup($group_id));
            $this->participants->pushCriteria(new ColumnMatches("email", "%" . $q . "%"));

            $rows = $this->participants->all();

            return $rows->pluck("id")->unique()->all();
    }




}