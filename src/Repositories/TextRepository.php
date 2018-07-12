<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Text;
// use Carbon\Carbon;
// use Cache;
//use Eventjuicer\Services\Repository;
use Bosnadev\Repositories\Eloquent\Repository;



class TextRepository extends Repository
{
    
    public function model()
    {
        return Text::class;
    }


}