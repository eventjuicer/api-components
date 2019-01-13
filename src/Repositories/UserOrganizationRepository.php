<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\User;
// use Carbon\Carbon;
// use Cache;

//use Eventjuicer\Services\Repository;
use Bosnadev\Repositories\Eloquent\Repository;

class UserOrganizationRepository extends Repository
{
    

    public function model()
    {
        return UserOrganization::class;
    }







}