<?php

namespace Eventjuicer\Crud\Tickets;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\Ticket;
use Eventjuicer\Repositories\EloquentTicketRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
// use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\SortBy;


class Fetch extends Crud  {



    protected $repo;
 
    
    function __construct(EloquentTicketRepository $repo){
        $this->repo = $repo;
    }

    public function getByRole(string $role){

        $this->setData();
        $this->repo->pushCriteria(new BelongsToEvent( $this->activeEventId() ));
		$this->repo->pushCriteria(new ColumnMatches("role", $role));
		return $this->repo->all();

    }

    public function get(){
        $this->repo->pushCriteria(new BelongsToEvent(   $this->activeEventId()  ));
        return $this->repo->all();
    }



    

}


