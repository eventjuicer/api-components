<?php

namespace Eventjuicer\Crud\CompanyMeetups;

use Eventjuicer\Crud\Crud;
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

    public function getAgreedByRelParticipantId($direction = "LTD"){

        $this->repo->makeModel();
        
        $rel_participant_id = (int) $this->getParam("rel_participant_id", 0);

        if(!$rel_participant_id){
            throw new \Exception("rel_participant_id missing!");
        }

        $this->repo->pushCriteria(new FlagEquals(  "rel_participant_id", $rel_participant_id ));
        $this->repo->pushCriteria(new FlagEquals(  "agreed", 1));
        $this->repo->pushCriteria(new FlagEquals(  "direction", $direction));

        return $this->repo->all();
    }

    public function getByParticipants( Collection $participants, $direction="P2C"){

        $this->repo->makeModel();

        $participant_ids = $participants->pluck("id")->all();
        
        $company_id = (int) $this->getParam("company_id");
        $rel_participant_id = (int) $this->getParam("rel_participant_id", 0);

        if($rel_participant_id){
            $this->repo->pushCriteria(new FlagEquals(  "rel_participant_id", $rel_participant_id ));
        }else{
            $this->repo->pushCriteria(new BelongsToCompany(  $company_id ));
        }

        $this->repo->pushCriteria(new FlagEquals( "direction", $direction ));
        $this->repo->pushCriteria(new WhereIn("participant_id",  $participant_ids ));

        return $this->repo->all();
    }

    /**
     * do not allow mass applications!
     */

    public function getAllForParticipantsInPipeline( Collection $participants){

        $this->repo->makeModel();

        $participant_ids = $participants->pluck("id")->all();
    
        $this->repo->pushCriteria(new FlagEquals( "direction", "LTD" ));
        $this->repo->pushCriteria(new WhereIn("participant_id",  $participant_ids ));
        $this->repo->pushCriteria(new ColumnIsNull("responded_at"));
    
        return $this->repo->all();
    }

    /**
     * do not allow 2 workshops!
     */

    public function getAllAgreedForParticipants( Collection $participants){

        $this->repo->makeModel();

        $participant_ids = $participants->pluck("id")->all();
    
        $this->repo->pushCriteria(new FlagEquals( "direction", "LTD" ));
        $this->repo->pushCriteria(new WhereIn("participant_id",  $participant_ids ));
        $this->repo->pushCriteria(new FlagEquals( "agreed", 1 ));
    
        return $this->repo->all();
    }


    /**
     * used by /workshopers
     */
    public function getMeetupsByDirection($direction="P2C"){

        $this->repo->makeModel();

        $event_id =   (int) $this->getParam("event_id");

        $this->repo->pushCriteria(new FlagEquals("direction", $direction));
        $this->repo->pushCriteria(new BelongsToEvent(  $event_id ));

        return $this->repo->all();

    }

    public function get($company_id=0){

        $this->repo->makeModel();

        $company_id = (int) $this->getParam("x-company_id", $company_id);
        $event_id = $this->activeEventId();

        $this->repo->pushCriteria(new BelongsToCompany(  $company_id ));
        $this->repo->pushCriteria(new BelongsToEvent(  $event_id ));

        $this->repo->pushCriteria(new OrderByCreatedAt("DESC"));

        // $this->repo->with(["company"]);

        return $this->repo->all();


    }




}