<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Group;
// use Carbon\Carbon;
// use Cache;

//use Eventjuicer\Services\Repository;
use Bosnadev\Repositories\Eloquent\Repository;

class GroupRepository extends Repository
{
    

    public function model()
    {
        return Group::class;
    }







}