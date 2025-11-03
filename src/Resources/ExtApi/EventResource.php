<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;

class EventResource extends Resource {

 
    public function toArray($request)
    {


      


       return [

            "id"        => (int) $this->id,
            "group_id" => $this->group_id,
            "name"      => $this->names,
            "loc"       => $this->location,
            "starts"    => $this->starts,
            "ends"      => $this->ends,

           
        ];
    }
}
