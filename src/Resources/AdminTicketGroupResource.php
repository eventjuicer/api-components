<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AdminTicketGroupResource extends Resource
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
            "name" => $this->name,
            "tags" =>  AdminTagResource::collection($this->oldtags),
            "booth" => $this->booth,
            "limit" => $this->limit
        ];
    }
}
