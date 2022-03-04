<?php

namespace Eventjuicer\Crud\CompanyData;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\CompanyData;
use Eventjuicer\Repositories\CompanyDataRepository;
use Eventjuicer\Services\CompanyData as CompanyDataPopulate;




use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\SortByAsc;
// use Eventjuicer\Crud\Traits\UseRouteInfo;


class Fetch extends Crud  {

    //use UseRouteInfo;

    protected $repo;
 
    
    function __construct(CompanyDataRepository $repo){
        $this->repo = $repo;

        (new CompanyDataPopulate($this->repo))->make( $this->getCompany() );

  
    }

    public function get($company_id=0){

        /**
         * populate with empty items.... 
         */

        $company_id = (int) $this->getParam("x-company_id", $company_id);

        $this->repo->pushCriteria(new BelongsToCompany(  $company_id ));

        $this->repo->pushCriteria( new SortByAsc("name") );
        $this->repo->pushCriteria( new FlagEquals("access", "company") );
        return $this->repo->all();

    }

    public function show($id){

        $this->repo->with(["company"]);

        return $this->repo->find($id);

    }

  
    

}


