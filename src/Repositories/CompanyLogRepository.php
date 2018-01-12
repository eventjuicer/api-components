<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\CompanyLog;
use Eventjuicer\Repositories\Repository;


class CompanyLogRepository extends Repository
{
    
    public function model()
    {
        return CompanyLog::class;
    }


}