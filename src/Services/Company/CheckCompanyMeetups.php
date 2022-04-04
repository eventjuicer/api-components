<?php 

namespace Eventjuicer\Services\Company;

use Eventjuicer\Crud\CompanyMeetups\Fetch;
use Eventjuicer\Services\ApiUserLimits;

class CheckCompanyMeetups extends Checkers {

    protected $repo;
    protected $limits;

    function __construct(Fetch $repo, ApiUserLimits $limits){
        $this->repo = $repo;
        $this->limits = $limits;
    }

    function getStatus(){
        
        $limit = $this->limits->meetups($this->repo->getRepo());
        $res = $this->repo->get();
        return ["max" => $limit + $res->count(), "current" => $res->count()];
    }

}
