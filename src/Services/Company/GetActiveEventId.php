<?php

namespace Eventjuicer\Services\Company;

use Eventjuicer\Models\Company;
use Eventjuicer\Models\Group;

class GetActiveEventId {

    protected $company;

    function __construct(Company $company){
        $this->company = $company;
    }

    function __toString(){
       return (string) Group::findOrFail( $this->company->group_id )->active_event_id;
    }

}