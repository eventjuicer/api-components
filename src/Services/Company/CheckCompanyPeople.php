<?php 

namespace Eventjuicer\Services\Company;
use Eventjuicer\Crud\CompanyPeople\Fetch;

class CheckCompanyPeople extends Checkers {

    protected $repo;

    function __construct(Fetch $repo){
        $this->repo = $repo;
    }

    function getStatus(){

        $this->repo->setData();


        $res = $this->repo->get()->filter(function($item){
            return !$item->disabled;
        })->mapToGroups(function($item){
            return [$item["role"] => $item["id"]];
        });

        if(!$res->count()){
            
        }

       
        

        return $res;
    }

}
