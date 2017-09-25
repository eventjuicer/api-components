<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Event;
// use Carbon\Carbon;
// use Cache;

//use Eventjuicer\Services\Repository;
use Bosnadev\Repositories\Eloquent\Repository;

class EventRepository extends Repository
{
    

    public function model()
    {
        return Event::class;
    }







}