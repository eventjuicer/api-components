<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PurchaseResource extends Resource
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
            
            "tickets" =>  TicketResource::collection($this->whenLoaded("tickets"))  
        ];  
    }
}
