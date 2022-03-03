<?php

namespace Eventjuicer\Crud\CompanyPeople;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\CompanyPeople;
use Eventjuicer\Repositories\CompanyPeopleRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\SortBy;
use Eventjuicer\Crud\Traits\UseRouteInfo;


class Fetch extends Crud  {

    //use UseRouteInfo;

    protected $repo;
 
    
    function __construct(CompanyPeopleRepository $repo){
        $this->repo = $repo;
    }

    public function get($company_id=0){


        $company_id = (int) $this->getParam("x-company_id", $company_id);

        $this->repo->pushCriteria(new BelongsToCompany(  $company_id ));
        $this->repo->pushCriteria(new FlagEquals("disabled", 0));
        $this->repo->pushCriteria( new SortBy("disabled", "ASC"));
        $this->repo->with(["company"]);

        return $this->repo->all();

    }

    public function show($id){

        $this->repo->with(["company"]);

        return $this->repo->find($id);

    }


    

}


