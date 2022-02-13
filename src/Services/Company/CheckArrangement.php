<?php 

namespace Eventjuicer\Services\Company;

use Eventjuicer\Resources\CheckerPurchasesResource;

class CheckArrangement extends Checkers {


    function getStatus(){
        $reps = new CompanyPurchases($this->company);
        $reps->setEventId($this->active_event_id);
        $res = $reps->get();

        $res = $res->filter(function($item){
            if($item->role == "service_external"){
                return true;
            }
            return false;
        })->values();

        return CheckerPurchasesResource::collection($res);
    }

}
