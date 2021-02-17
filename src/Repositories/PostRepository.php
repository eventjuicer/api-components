<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Post;
// use Carbon\Carbon;
// use Cache;

//use Eventjuicer\Services\Repository;
use Bosnadev\Repositories\Eloquent\Repository;

class PostRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;


    public function model()
    {
        return Post::class;
    }







}