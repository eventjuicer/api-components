<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Company;
use Eventjuicer\Repositories\Repository;

use Carbon\Carbon;
use Closure;

class CompanyRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return Company::class;
    }


  


}
