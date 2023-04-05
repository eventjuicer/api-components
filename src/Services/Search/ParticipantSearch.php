<?php namespace Eventjuicer\Services\Search;

//use Eventjuicer\Repositories\Repository;

use  Bosnadev\Repositories\Eloquent\Repository;

use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Models\ParticipantFields;
use Eventjuicer\Models\Participant;
use Eventjuicer\Services\Hashids;

class ParticipantSearch {


protected $result;


function __construct(Repository $repo, int $event_id, string $q, $addFields = []){

        $q = trim($q);

        if(!$q || mb_strlen($q) < 4){
            return;
        }

        if( strpos($q, "@")!==false ){

            $repo->with(["fields"]);
            $repo->pushCriteria(new BelongsToEvent($event_id));
            $repo->pushCriteria(new ColumnMatches("email", "%" . $q . "%"));
                
            $this->result = $repo->all();
        
        }

        else if( is_numeric($q) ){

            /***
             * PHONE?
             */
            $rows = ParticipantFields::with("participant", "participant.fields")->where("event_id", $event_id)->where("field_id", 8)->where("field_value", "LIKE", $q."%")->get()->pluck("participant");  

            $this->result = $rows;   

        }else if( strpos($q, "!") === 0 ){

            $lookup = Participant::find( (new Hashids())->decode( substr($q, 1) )  );

            var_dump($lookup);

            $this->result =  $lookup && $lookup->event_id==$event_id ? collect([ $lookup ]): collect([]);
        }
        else{
  
                $rows = ParticipantFields::with("participant", "participant.fields")->where("event_id", $event_id)->whereIn("field_id", array_merge([3,11], (array) $addFields))->where("field_value", "LIKE", $q."%")->get()->pluck("participant");
                //$rows2 = collect([]);

                // if(strlen($q)>4){
                //     $repo->with(["fields"]);
                //     $repo->pushCriteria(new BelongsToEvent($event_id));
                //     $repo->pushCriteria(new ColumnMatches("email", "%" . $q . "%"));    
                //     $rows2 = $repo->all();
                // }

                //$rows = $rows1->merge( $rows2  );
            
                $this->result = $rows;//->unique("id")->values();

        }
}


function results()
{
    return $this->result ? $this->result : collect([]);
}




}