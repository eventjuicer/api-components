<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\CompanyPerformance;
use Eventjuicer\Repositories\Repository;


class CompanyPerformanceRepository extends Repository{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return CompanyPerformance::class;
    }




}

