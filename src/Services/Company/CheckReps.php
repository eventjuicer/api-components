<?php 

namespace Eventjuicer\Services\Company;

use Eventjuicer\Services\Exhibitors\CompanyReps;
use Eventjuicer\Resources\CheckerParticipantsResource;

class CheckReps extends Checkers {


    function getStatus(){
        $reps = new CompanyReps($this->company);
        $reps->setEventId($this->active_event_id);
        $res = $reps->get("representative", false);
        return CheckerParticipantsResource::collection( $res );
    }

}
