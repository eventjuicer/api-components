<?php

namespace Eventjuicer\Crud\CompanyParticipants;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\Participant;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\WhereHas;
// use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\SortBy;


class Fetch extends Crud  {


    protected $repo;

    
    function __construct(ParticipantRepository $repo){
        $this->repo = $repo;
    }

    public function get($company_id=0){

        $this->setData();
  
        $company_id = (int) $this->getParam("x-company_id", $company_id);

        $this->repo->pushCriteria(new BelongsToCompany(  $company_id ));

        $this->repo->pushCriteria( new WhereHas("tickets", function($q){
            $q->where("role", "representative");
            // $q->where("event_id", $this->eventId);
         }));
        

        $this->repo->with(["fields","tickets"]);

        return $this->repo->all();

    }

  


    

}


