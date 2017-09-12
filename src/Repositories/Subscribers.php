<?php

namespace Eventjuicer\Repositories;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
//use Bosnadev\Repositories\Eloquent\Repository;

use Eventjuicer\Repositories\Repository;

use Eventjuicer\Models\Participant;


use Context;

use Carbon\Carbon;

use Cache;

use DB;


use Illuminate\Database\Eloquent\Collection;


use Eventjuicer\Repositories\Criteria\BelongsToOrganizer;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;
use Eventjuicer\Repositories\Criteria\SortByDesc;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\TaggedWith;
use Eventjuicer\Repositories\Criteria\Limit;
use Eventjuicer\Repositories\Criteria\OlderThanDateTime;
use Eventjuicer\Repositories\Criteria\YoungerThanDateTime;
use Eventjuicer\Repositories\Criteria\RelTableHas;

class Subscribers extends Repository {



    protected $preventCriteriaOverwriting = false;


    
    public function model()
    {
        return Participant::class;
    }


    
    public function monthly($months = 12)
    {

         return $this->cached(null, 5, function() use ($months)
        {
              return ;


        });


    }












}
