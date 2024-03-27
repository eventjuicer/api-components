<?php

namespace Eventjuicer\Crud\CompanyVipcodes;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\CompanyVipcodeRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\SortByDesc;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Services\ApiUserLimits;





class Fetch extends Crud  {


    protected $repo;
    protected $create;
    protected $howmany = 5;
    protected $limits;

    function __construct(Create $create, ApiUserLimits $limits){
        $this->create = $create;
        $this->limits = $limits;
    }

    function makeRepository(){
        return app(CompanyVipcodeRepository::class);
    }

    public function getTargetCount(){
        //handle companydata tweak....

        if($this->getUser()){
             return (int)  $this->limits->vip(CompanyVipcodeRepository::class);

        }else{
            //public endpoint

            return 0;
        }

       
    }



    public function get($company_id=0){

        $company_id = (int) $this->getParam("x-company_id", $company_id);

        /**
         * mark expired as expired
         */

        $this->expireExpired( $company_id );

        $res = $this->_get($company_id);

        $missing =  $this->getTargetCount(); //10

        if($missing > 0){

            foreach(range(1, $missing) as $i){ 
                 $this->create->create($company_id, $i);
            }

            $res = $this->_get($company_id);            
        }

        return $res;
    }


    protected function expireExpired($company_id=0){

        foreach($this->_get($company_id) as $item){
            if( trim($item->email) && !$item->participant_id && $item->updated_at->addDays(5)->isPast() ){
                $item->expired =  1;
                //do not update updated_at
                $item->timestamps = false;
                $item->save();
                $item->timestamps = true;
            }
        } 
    
    }


    public function _get($company_id=0){

        $event_id = $this->activeEventId();

        $repo = $this->makeRepository();

        $repo->pushCriteria(new BelongsToCompany(  $company_id ));
        $repo->pushCriteria(new BelongsToEvent(  $event_id ));

        $repo->pushCriteria(new FlagEquals("expired", 0));
        $repo->pushCriteria( new SortByDesc("created_at"));

        $repo->with(["participant.fields"]);
        return $repo->all();

    }

    public function getByCode($code){

        $repo = $this->makeRepository();

        $repo->pushCriteria(new FlagEquals("code", (string) $code));
        return $repo->all()->first();

    }


    public function show($id){

        $repo = $this->makeRepository();

        $repo->with(["company"]);
        return $repo->find($id);

    }


    

}


