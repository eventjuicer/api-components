<?php

namespace Eventjuicer\Crud\Users;

use Eventjuicer\Crud\Crud;
use Illuminate\Support\Collection;
use Eventjuicer\Repositories\UserRepository;

// use Eventjuicer\Repositories\Criteria\BelongsToCompany;
// use Eventjuicer\Repositories\Criteria\FlagEquals;
// use Eventjuicer\Repositories\Criteria\SortByAsc;
use Eventjuicer\Repositories\Criteria\RelTableHas;


class GetUsersByOrganization extends Crud  {

    public function get(){

        $rep = app(UserRepository::class);
    }
    

}



