<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TicketResource extends Resource
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
            "id"    => (int) $this->id,
            "name" => array_get($this->names, "pl"),
            "role" => $this->role
        ];
    }
}
