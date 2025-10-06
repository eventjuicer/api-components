<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;

class TicketGroupResource extends Resource
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
            "tags" =>  LegacyTagResource::collection($this->oldtags),
            "booth" => $this->booth,
            "limit" => $this->limit
        ];
    }
}
