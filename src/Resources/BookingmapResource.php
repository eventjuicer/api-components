<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 

class BookingmapResource extends Resource
{

    public function toArray($request)
    {   
        
        $data = $this->data;
        $data["booths"] = json_decode($this->data["booths"]);
 		$data["id"] = $this->id;

        return $data;
    }
}



