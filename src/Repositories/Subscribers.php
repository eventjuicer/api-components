<?php


namespace Repositories;


use Bosnadev\Repositories\Contracts\RepositoryInterface;
//use Bosnadev\Repositories\Eloquent\Repository;

use Services\Repository;

use Eventjuicer\Participant;


use Context;

use Carbon\Carbon;

use Cache;

use DB;


use Illuminate\Database\Eloquent\Collection;


use Repositories\Criteria\BelongsToOrganizer;
use Repositories\Criteria\BelongsToGroup;
use Repositories\Criteria\SortByDesc;
use Repositories\Criteria\FlagEquals;
use Repositories\Criteria\TaggedWith;
use Repositories\Criteria\Limit;
use Repositories\Criteria\OlderThanDateTime;
use Repositories\Criteria\YoungerThanDateTime;
use Repositories\Criteria\RelTableHas;

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
