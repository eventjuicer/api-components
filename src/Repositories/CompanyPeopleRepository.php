<?php

namespace Eventjuicer\Repositories;

//use Eventjuicer\Models\CompanyRepresentative;
use Eventjuicer\Models\CompanyPeople;

use Eventjuicer\Repositories\Repository;
 

class CompanyPeopleRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return CompanyPeople::class;
    }


}

