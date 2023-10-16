<?php

namespace Eventjuicer\Crud\CompanyMeetups;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\Participant;
use Eventjuicer\Repositories\MeetupRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\WhereIn;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\OrderByCreatedAt;
use Eventjuicer\Repositories\Criteria\ColumnIsNull;
use Illuminate\Support\Collection;

class Fetch extends Crud  {

    protected $repo;

    function __construct(MeetupRepository $repo){
        $this->repo = $repo;
    }

    function makeMeetupRepository(){
        return app(MeetupRepository::class);
    }

    public function getAgreedByRelParticipantId($direction = "LTD"){

        $repo = $this->makeMeetupRepository();
        
        $rel_participant_id = (int) $this->getParam("rel_participant_id", 0);

        if(!$rel_participant_id){
            throw new \Exception("rel_participant_id missing!");
        }

        $repo->pushCriteria(new FlagEquals(  "rel_participant_id", $rel_participant_id ));
        $repo->pushCriteria(new FlagEquals(  "agreed", 1));
        $repo->pushCriteria(new FlagEquals(  "direction", $direction));

        return $repo->all();
    }

    public function getByParticipants( Collection $participants, $direction="P2C"){

        $repo = $this->makeMeetupRepository();

        $participant_ids = $participants->pluck("id")->all();
        
        $company_id = (int) $this->getParam("company_id");
        $rel_participant_id = (int) $this->getParam("rel_participant_id", 0);

        if($rel_participant_id){
            $repo->pushCriteria(new FlagEquals(  "rel_participant_id", $rel_participant_id ));
        }else{
            $repo->pushCriteria(new BelongsToCompany(  $company_id ));
        }

        $repo->pushCriteria(new FlagEquals( "direction", $direction ));
        $repo->pushCriteria(new WhereIn("participant_id",  $participant_ids ));

        return $repo->all();
    }

    public function checkTimeConflict(Collection $participants){

        $rel_participant_id = (int) $this->getParam("rel_participant_id", 0);

        $presentationTimes = Participant::find($rel_participant_id)->fields->where("name","presentation_time")->first();

        $presentationTime = $presentationTimes? $presentationTimes->pivot->field_value: "";

        $openOrAccepted = $this->getAllForParticipantsInPipelineOrAccepted($participants);

        $openOrAcceptedHrs = $openOrAccepted->pluck("presenter.fields")->collapse()->where("name", "presentation_time")->pluck("pivot.field_value")->all();

        return in_array($presentationTime, $openOrAcceptedHrs);

    }

    /**
     * GET ALL OPEN OR ACCEPTED....
     */

     public function getAllForParticipantsInPipelineOrAccepted( Collection $participants){

        $repo = $this->makeMeetupRepository();

        $participant_ids = $participants->pluck("id")->all();
    
        $repo->pushCriteria(new FlagEquals( "direction", "LTD" ));
        $repo->pushCriteria(new WhereIn("participant_id",  $participant_ids ));
        $repo->with(["presenter.fields"]);
    
        $all = $repo->all();

        $filtered = $all->filter(function($meetup){
            return !$meetup->responded_at || $meetup->agreed;
        });

        return $filtered;
    }


    /**
     * do not allow mass applications!
     */

    public function getAllForParticipantsInPipeline( Collection $participants){

        $repo = $this->makeMeetupRepository();

        $participant_ids = $participants->pluck("id")->all();
    
        $repo->pushCriteria(new FlagEquals( "direction", "LTD" ));
        $repo->pushCriteria(new WhereIn("participant_id",  $participant_ids ));
        $repo->pushCriteria(new ColumnIsNull("responded_at"));
    
        return $repo->all();
    }

    /**
     * do not allow 2 workshops!
     */

    public function getAllAgreedForParticipants( Collection $participants){

        $repo = $this->makeMeetupRepository();

        $participant_ids = $participants->pluck("id")->all();
    
        $repo->pushCriteria(new FlagEquals( "direction", "LTD" ));
        $repo->pushCriteria(new WhereIn("participant_id",  $participant_ids ));
        $repo->pushCriteria(new FlagEquals( "agreed", 1 ));
    
        return $repo->all();
    }


    /**
     * used by /workshopers
     */
    public function getMeetupsByDirection($direction="P2C"){

        $repo = $this->makeMeetupRepository();

        $event_id =   (int) $this->getParam("event_id");

        $repo->pushCriteria(new FlagEquals("direction", $direction));
        $repo->pushCriteria(new BelongsToEvent(  $event_id ));

        return $repo->all();

    }


    /** RESTRICTED  */

    public function getAgreedByDirection($direction = "LTD"){

        $repo = $this->makeMeetupRepository();

        $company_id = (int) $this->getParam("company_id", 0);
        $event_id = $this->activeEventId();

        if(!$company_id || !$event_id){
            throw new \Exception("getAgreedByDirection / bad params");
        }

        $repo->pushCriteria(new BelongsToCompany(  $company_id ));
        $repo->pushCriteria(new BelongsToEvent(  $event_id ));
        $repo->pushCriteria(new FlagEquals( "direction", $direction ));
        $repo->pushCriteria(new FlagEquals( "agreed", 1));

        return $repo->all();

    }


    /** CONSOLE  */

    public function getAllForEventByDirection($direction = "LTD"){

        $repo = $this->makeMeetupRepository();

        $event_id = (int) $this->getParam("event_id");

        if(! (int) $event_id){
            throw new \Exception("getAllAgreedByDirection / bad params");
        }

        $repo->pushCriteria(new BelongsToEvent(  $event_id ));
        $repo->pushCriteria(new FlagEquals( "direction", $direction ));

        return $repo->all();

    }



    public function get($company_id=0){

        $repo = $this->makeMeetupRepository();

        $company_id = (int) $this->getParam("x-company_id", $company_id);
        $event_id = $this->activeEventId();

        $repo->pushCriteria(new BelongsToCompany(  $company_id ));
        $repo->pushCriteria(new BelongsToEvent(  $event_id ));

        $repo->pushCriteria(new OrderByCreatedAt("DESC"));

        // $this->repo->with(["company"]);

        return $repo->all();


    }




}