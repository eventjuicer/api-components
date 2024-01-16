<?php

namespace Eventjuicer\Crud\Tickets;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\EloquentTicketRepository;
use Eventjuicer\Repositories\Criteria\WhereIn;



class GetTicketsByIds extends Crud  {

    protected $repo;
     
    function __construct(EloquentTicketRepository $repo){
        $this->repo = $repo;
    }

    public function get(array $ids){

        $this->repo->pushCriteria(new WhereIn( "id", $ids));
		return $this->repo->all();

    }


    

}


