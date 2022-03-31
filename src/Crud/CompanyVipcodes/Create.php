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

    function validates(){
        return $this->isValid([
            'email' => 'required|email:rfc,dns',
        ]);
    }

    public function create(){

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

    public function update($id){

        if(!$this->validates()){
            return null;
        }

        $email = $this->getParam("email");

        $this->repo->update(["email"=> $email ], $id);
        
        return $this->find($id);

    }

    protected function generateCode(){

        return sha1(implode("_", array(
            $this->getUser()->token, 
            $this->getCompanyId(),
            microtime(),
            mt_rand()
        )));

    }

    

}


