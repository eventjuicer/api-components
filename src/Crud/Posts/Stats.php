<?php

namespace Eventjuicer\Crud\Posts;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\PostRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;


class Stats extends Crud  {

    private $repo;

    
    function __construct(PostRepository $repo){
        $this->repo = $repo;
    }

    public function get(){

        $company_id = $this->getParam("company_id");
        $this->repo->pushCriteria( new BelongsToCompany( $company_id ) );
        return $this->repo->all();

    }



}


