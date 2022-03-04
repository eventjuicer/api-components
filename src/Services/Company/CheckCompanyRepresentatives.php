<?php 

namespace Eventjuicer\Services\Company;

use Eventjuicer\Crud\CompanyRepresentatives\Fetch;

class CheckCompanyRepresentatives extends Checkers {

    protected $repo;

    function __construct(Fetch $repo){
        $this->repo = $repo;
    }

    function getStatus(){
        
        $this->repo->setData();

        $res = $this->repo->get();
      
        return ["current" => $res->count()];

    }

}
