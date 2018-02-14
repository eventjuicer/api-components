<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Scan;
// use Carbon\Carbon;
// use Cache;

//use Eventjuicer\Services\Repository;
use Bosnadev\Repositories\Eloquent\Repository;

class ScanRepository extends Repository
{
    

    public function model()
    {
        return Scan::class;
    }







}