<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 
class PublicPreBookingResource extends Resource {

    protected static $threshold = 0;

    public static function setThreshold($value){
       self::$threshold = (int) $value; 
    }

    public function toArray($request){   

        $data = array(
            "sessid" => $this->sessid,
            "item_uid" => $this->item_uid,
            "ticket_id" => $this->ticket_id,
            "remaining" => ($this->blockedon + self::$threshold) - time()
        );
        
        return $data;
    }

}



