<?php

namespace Eventjuicer\Crud\CompanyVipcodes;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\CompanyVipcodeRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\SortBy;
use Eventjuicer\Repositories\Criteria\FlagEquals;


class Fetch extends Crud  {


    protected $repo;
    protected $create;
    protected $howmany = 10;


    function __construct(CompanyVipcodeRepository $repo, Create $create){
        $this->repo = $repo;
        $this->create = $create;
    }

    public function getTargetCount(){
        //handle companydata tweak....

        return (int) $this->howmany;

    }



    public function get($company_id=0){

        $company_id = (int) $this->getParam("x-company_id", $company_id);

        $res = $this->_get($company_id);

        $missing =  $this->getTargetCount() - $res->count();

        if($missing > 0){

            foreach(range(1, $missing) as $i){ 
                 $this->create->create($company_id, $i);
            }

            $res = $this->_get($company_id);            
        }

        return $res;
    }


    public function _get($company_id=0){

        $this->repo->pushCriteria(new BelongsToCompany(  $company_id ));
        $this->repo->pushCriteria(new FlagEquals("expired", 0));
        $this->repo->pushCriteria( new SortBy("participant_id", "ASC"));
        $this->repo->with(["participant.fields"]);
        return $this->repo->all();

    }

    public function show($id){

        $this->repo->with(["company"]);
        return $this->repo->find($id);

    }


    

}


