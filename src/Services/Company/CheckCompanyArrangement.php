<?php 

namespace Eventjuicer\Services\Company;

use Eventjuicer\Crud\CompanyPurchasedTickets\Fetch;
use Eventjuicer\Crud\CompanyPurchasedTickets\FilterNonCancelled;
use Eventjuicer\Crud\CompanyPurchasedTickets\FilterActiveEvent;
use Eventjuicer\Crud\CompanyPurchasedTickets\FilterArrangement;

class CheckCompanyArrangement extends Checkers {

    
    protected $repo;

    function __construct(Fetch $repo){
        $this->repo = $repo;
    }


    function getStatus(){


        $this->repo->setFilter(FilterNonCancelled::class);
        $this->repo->setFilter(FilterActiveEvent::class);
        $this->repo->setFilter(FilterArrangement::class);

        $res = $this->repo->filter(
            $this->repo->get()
        );

        $tickets = $res->pluck("ticket");

        return ["current" => $tickets->count()];


    }

}
