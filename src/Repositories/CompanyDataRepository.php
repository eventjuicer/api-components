<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\CompanyData;
use Eventjuicer\Repositories\Repository;

 

class CompanyDataRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return CompanyData::class;
    }




}

