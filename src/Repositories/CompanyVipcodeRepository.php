<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\CompanyVipcode;
use Eventjuicer\Repositories\Repository;

class CompanyVipcodeRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return CompanyVipcode::class;
    }




}

