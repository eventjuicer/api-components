<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 
class PublicPreBookingResource extends Resource {

    public function toArray($request){   

        $data = array(
            "sessid" => $this->sessid,
            "formdata" => $this->ticketdata,
            "seconds_ago" => time() - $this->blockedon

        );
        
        return $data;
    }

}



