<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\User;
// use Carbon\Carbon;
// use Cache;

//use Services\Repository;
use Bosnadev\Repositories\Eloquent\Repository;

class UserRepository extends Repository
{
    

    public function model()
    {
        return User::class;
    }







}