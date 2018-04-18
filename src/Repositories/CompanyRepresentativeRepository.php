<?php

namespace Eventjuicer\Repositories;

//use Eventjuicer\Models\CompanyRepresentative;
use Eventjuicer\Models\Participant;

use Eventjuicer\Repositories\Repository;
 

class CompanyRepresentativeRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return Participant::class;
    }


}

