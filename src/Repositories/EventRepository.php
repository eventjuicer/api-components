<?php

namespace Repositories;

use Models\Event;
// use Carbon\Carbon;
// use Cache;

//use Services\Repository;
use Bosnadev\Repositories\Eloquent\Repository;

class EventRepository extends Repository
{
    

    public function model()
    {
        return Event::class;
    }







}