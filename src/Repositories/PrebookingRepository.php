<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Prebooking;
use Eventjuicer\Repositories\Repository;

use Carbon\Carbon;
use Uuid;

class PrebookingRepository extends Repository {
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return Prebooking::class;
    }





}