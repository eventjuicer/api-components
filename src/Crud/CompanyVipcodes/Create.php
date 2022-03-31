<?php

namespace Eventjuicer\Crud\CompanyVipcodes;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\CompanyVipcode;
use Eventjuicer\Repositories\CompanyVipcodeRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\SortBy;


class Create extends Crud  {


    protected $repo;
 
    function __construct(CompanyVipcodeRepository $repo){
        $this->repo = $repo;
    }

    public function create($i=0){

        $data = [
            "organizer_id" => $this->activeGroup()->organizer_id,
            "group_id" => $this->activeGroup()->id,
            "event_id" => $this->activeEventId(),
            "company_id" => $this->getCompanyId(),
            "code" => $this->generateCode(),
            "email" => "",
            "participant_id" => 0
        ];

        $this->repo->saveModel($data);
        $this->repo->makeModel();

    }

    public function generateCode(){

        return sha1(implode("_", array(
            $this->getUser()->token, 
            $this->getCompanyId(),
            microtime(),
            mt_rand()
        )));

    }

    

}


