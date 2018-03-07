<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 

class PublicTicketGroupResource extends Resource
{

    public function toArray($request)
    {       
   
        return [

            "id"                => $this->id,        
            "name"              => $this->name,
            "descriptions"      => $this->descriptions,
            "map"               => $this->booth,
            "limit"             => $this->limit,

            "offered"           => isset($this->offered) ? $this->offered  : null,
            "sold"              => isset($this->sold) ?  $this->sold  : null,
            "customers"         => isset($this->customers) ? $this->customers  : null,
            
            "tickets"           => PublicTicketResource::collection($this->whenLoaded("tickets")),
        ];
    }
}



