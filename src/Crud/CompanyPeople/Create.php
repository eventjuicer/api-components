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

    protected $repo;
    
    function __construct(CompanyPeopleRepository $repo){
        $this->repo = $repo;
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

       $data = $this->getData();

        //resolved by AppServiceProvider
        $data["group_id"] = (int) $this->getParam("x-group_id", 0);
        $data["company_id"] = (int) $this->getParam("x-company_id", 0);

        $this->repo->saveModel($data);

        return $this->find( $this->repo->getId() );
    }


    public function update($id){

        if(!$this->validates()){
            return null;
        }

        $this->repo->update($this->getData(), $id);
        return $this->find($id);
    }

    public function delete($id){

        $this->repo->update(["disabled"=>1], $id);
        return $this->find($id);
    }


    protected function getData(){
        
        $fname = $this->getParam("fname", "");
        $lname = $this->getParam("lname", "");
        $email = $this->getParam("email", "");
        $role = $this->getParam("role", "");
        $phone = (int) $this->getParam("phone", 0);

        return compact(
            "fname",
            "lname",
            "email",
            "phone",
            "role"
        );
    }



}


