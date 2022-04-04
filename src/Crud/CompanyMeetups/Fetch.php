<?php

namespace Eventjuicer\Crud\CompanyMeetups;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\MeetupRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;

// use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\OrderByCreatedAt;

class Fetch extends Crud  {

    protected $repo;

    function __construct(MeetupRepository $repo){
        $this->repo = $repo;
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


