<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Visitor;
use Eventjuicer\Repositories\Repository;

use Carbon\Carbon;
use Closure;

class VisitorRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return Visitor::class;
    }




}

