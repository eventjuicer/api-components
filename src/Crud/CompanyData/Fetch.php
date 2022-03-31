<?php

namespace Eventjuicer\Crud\CompanyData;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\CompanyData;
use Eventjuicer\Repositories\CompanyDataRepository;
use Eventjuicer\Services\CompanyData as CompanyDataPopulate;
use Illuminate\Support\Collection;



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

    public function get($company_id=0, string $access="company"){

        /**
         * populate with empty items.... 
         */

        $company_id = (int) $this->getParam("x-company_id", $company_id);
        $this->repo->pushCriteria(new BelongsToCompany(  $company_id ));
        $this->repo->pushCriteria( new SortByAsc("name") );

        if($access){
            $this->repo->pushCriteria( new FlagEquals("access", $access) );
        }

        return $this->repo->all();
    }

    public function show($id){

        return $this->find($id);

    }

    public function toArray(Collection $coll){

        return $coll->mapWithKeys(function($item){
            return [$item->name => $item->value];
        })->all();

    }

    public function getByCompanyIdAndName($company_id, $name){

        $this->repo->pushCriteria(new BelongsToCompany((int) $company_id));
        $this->repo->pushCriteria(new FlagEquals("name", (string) $name));
        return $this->repo->all()->first();

    }
  
    

}



