<?php

namespace Eventjuicer\Crud\TicketDownloads;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\TicketDownloadRepository;

use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\WhereIn;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;

class Fetch extends Crud  {

    protected $repo;

    
    function __construct(TicketDownloadRepository $repo){
        $this->repo = $repo;
    }

    public function get($event_id){

        $this->setData();

        $this->repo->pushCriteria(new BelongsToEvent( (int) $event_id));

        $res = $this->repo->all();

        return $res;
    }


  


    

}

