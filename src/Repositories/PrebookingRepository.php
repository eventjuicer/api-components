<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\PreBooking;
use Eventjuicer\Repositories\Repository;

use Carbon\Carbon;
use Uuid;

class PrebookingRepository extends Repository {
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return PreBooking::class;
    }





}