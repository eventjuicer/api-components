<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PublicEventResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
       return [

            "id"        => (int) $this->id,
            "name"      => $this->names,
            "loc"       => $this->location,
            "starts"    => $this->starts,
            "ends"      => $this->ends,
               
        ];
    }
}
