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
        })->keyBy("role");

        return ["max" => 3, "current" => $res->count()];
    }

}
