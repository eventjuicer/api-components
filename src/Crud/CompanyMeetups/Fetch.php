<?php

namespace Eventjuicer\Crud\CompanyMeetups;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\MeetupRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\WhereIn;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\OrderByCreatedAt;
use Illuminate\Support\Collection;

class Fetch extends Crud  {

    protected $repo;

    function __construct(MeetupRepository $repo){
        $this->repo = $repo;
    }


    public function getByParticipants( Collection $participants ){

        $participant_ids = $participants->pluck("id")->all();
        $company_id = (int) $this->getParam("company_id");
        $event_id =   (int) $this->getParam("event_id");

        $this->repo->pushCriteria(new BelongsToCompany(  $company_id ));
        $this->repo->pushCriteria(new FlagEquals(  "direction", "P2C" ));
        $this->repo->pushCriteria(new BelongsToEvent(  $event_id ));
        $this->repo->pushCriteria(new WhereIn("participant_id",  $participant_ids ));

        return $this->repo->all();
    }

    public function getAgreedByDirection($direction="P2C"){

        $event_id =   (int) $this->getParam("event_id");

        $this->repo->pushCriteria(new FlagEquals("direction", $direction));
        $this->repo->pushCriteria(new BelongsToEvent(  $event_id ));

        return $this->repo->all();

    }

    public function get($company_id=0){


        $company_id = (int) $this->getParam("x-company_id", $company_id);
        $event_id = $this->activeEventId();

        $this->repo->pushCriteria(new BelongsToCompany(  $company_id ));
        $this->repo->pushCriteria(new BelongsToEvent(  $event_id ));

        $this->repo->pushCriteria(new OrderByCreatedAt("DESC"));

        // $this->repo->with(["company"]);

        return $this->repo->all();


    }




}