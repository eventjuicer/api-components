<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Contracts\SavesPaidOrder;

class PublicPreBookingResource extends Resource {

    public function toArray($request){   

        $lockingClass = app(SavesPaidOrder::class);

        $data = array(
            "sessid" => $this->sessid,
            "item_uid" => $this->item_uid,
            "ticket_id" => $this->ticket_id,
            "remaining" => ($this->blockedon + $lockingClass->getThreshold()) - time()
        );
        
        return $data;
    }

}



