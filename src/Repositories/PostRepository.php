<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Post;
// use Carbon\Carbon;
// use Cache;

//use Services\Repository;
use Bosnadev\Repositories\Eloquent\Repository;

class PostRepository extends Repository
{
    

    public function model()
    {
        return Post::class;
    }







}