<?php

namespace Eventjuicer\Crud\CompanyVipcodes;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\CompanyVipcodeRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\SortBy;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Services\ApiUserLimits;





class Fetch extends Crud  {


    protected $repo;
    protected $create;
    protected $howmany = 10;
    protected $limtis;

    function __construct(CompanyVipcodeRepository $repo, Create $create, ApiUserLimits $limits){
        $this->repo = $repo;
        $this->create = $create;
        $this->limits = $limits;
    }

    public function getTargetCount(){
        //handle companydata tweak....

        if($this->getUser()){
            return (int)  $this->limits->vip($this->repo);
        }else{
            //public endpoint

            return 0;
        }

       
    }



    public function get($company_id=0){

        $company_id = (int) $this->getParam("x-company_id", $company_id);

        $res = $this->_get($company_id);

        $missing =  $this->getTargetCount();

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

    public function getByCode($code){
        $this->repo->pushCriteria(new FlagEquals("code", (string) $code));
        return $this->repo->all()->first();

    }


    public function show($id){

        $this->repo->with(["company"]);
        return $this->repo->find($id);

    }


    

}


