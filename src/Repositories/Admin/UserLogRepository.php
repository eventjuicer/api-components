<?php

namespace Eventjuicer\Repositories\Admin;

use Eventjuicer\Models\UserLog;
use Eventjuicer\Repositories\Repository;


class UserLogRepository extends Repository {
    

    protected $preventCriteriaOverwriting = false;


    public function model(){
        return UserLog::class;
    }

}