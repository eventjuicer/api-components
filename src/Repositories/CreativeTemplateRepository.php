<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\CreativeTemplate;
// use Carbon\Carbon;
// use Cache;

//use Eventjuicer\Services\Repository;
use Bosnadev\Repositories\Eloquent\Repository;

class CreativeTemplateRepository extends Repository
{
    

    public function model()
    {
        return CreativeTemplate::class;
    }







}