<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Host;
// use Carbon\Carbon;
// use Cache;

//use Eventjuicer\Services\Repository;
use Bosnadev\Repositories\Eloquent\Repository;

class HostRepository extends Repository
{
    

    public function model()
    {
        return Host::class;
    }







}