<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Repositories\Repository;
use Eventjuicer\Models\Partner;

 
class PartnerRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;


    public function model()
    {
        return Partner::class;
    }


}