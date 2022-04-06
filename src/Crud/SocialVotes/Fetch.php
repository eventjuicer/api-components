<?php

namespace Eventjuicer\Crud\SocialVotes;

use Eventjuicer\Crud\Crud;

use Eventjuicer\Repositories\Criteria\BelongsToOrganizer;
use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\SortBy;
use Eventjuicer\Repositories\Criteria\WhereHas;
use Eventjuicer\Repositories\SocialVoteRepository;

class Fetch extends Crud  {

    protected $repo;

    function __construct(SocialVoteRepository $repo){
        $this->repo = $repo;
    }


    function getByOrganizerId($organizer_id=0){

        $this->repo->pushCriteria(new BelongsToOrganizer((int) $organizer_id));
        $this->repo->with(["voteable"]);
        $res = $this->repo->all();

        return $res;
    }
 
   

}


