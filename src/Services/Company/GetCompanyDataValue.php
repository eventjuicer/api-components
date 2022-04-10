<?php

namespace Eventjuicer\Services\Company;

use Eventjuicer\Models\Company;

class GetCompanyDataValue {

    protected $companydata;

    function __construct(Company $company){
        $this->companydata = $company->data;
    }

    function get($name){

        $query = $this->companydata->where("name", $name)->first();

        return $query? $query->value : null;
    }
}