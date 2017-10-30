<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Creative;
// use Carbon\Carbon;
// use Cache;

//use Eventjuicer\Services\Repository;
use Bosnadev\Repositories\Eloquent\Repository;

class CreativeRepository extends Repository
{
    

    public function model()
    {
        return Creative::class;
    }







}