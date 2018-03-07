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

            "agg" => isset($this->agg) ? $this->agg  : [],
            
            "tickets"           => PublicTicketResource::collection($this->whenLoaded("tickets")),
        ];
    }
}



