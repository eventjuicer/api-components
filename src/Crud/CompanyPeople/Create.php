<?php

namespace Eventjuicer\Crud\CompanyPeople;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\CompanyPeople;
use Eventjuicer\Repositories\CompanyPeopleRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\SortBy;
use Eventjuicer\Crud\Traits\UseRouteInfo;
use Illuminate\Validation\Rule;


class Create extends Crud  {

    //use UseRouteInfo;

    private $repo;
    private $fetch;
    
    function __construct(Fetch $fetch, CompanyPeopleRepository $repo){
        $this->repo = $repo;
        $this->fetch = $fetch;
    }

    function validates(){
        
        return $this->isValid([
            'fname' => 'required|min:2|max:255',
            'lname' => 'required|min:2|max:255',
            'email' => 'required|email:rfc,dns',
            'role' => ['required', Rule::in(['pr_manager', 'sales_manager', 'event_manager']) ],
            'phone' => 'required|numeric|digits_between:8,20',

        ]);

    }

    public function create(){

        if(!$this->validates()){
            return null;
        }

        $fname = $this->getParam("fname", "");
        $lname = $this->getParam("lname", "");
        $email = $this->getParam("email", "");
        $role = $this->getParam("role", "");
        $phone = (int) $this->getParam("phone", 0);

        //resolved by AppServiceProvider
        $group_id = (int) $this->getParam("x-group_id", 0);
        $company_id = (int) $this->getParam("x-company_id", 0);

        $this->repo->saveModel(compact(
            "fname",
            "lname",
            "email",
            "phone",
            "role",
            "group_id",
            "company_id"
        ));

        return $this->fetch->show( $this->repo->getId() );



    }

  
    

}


