<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 

class PublicTicketGroupResource extends Resource
{

    public function toArray($request)
    {   
    

        return [

            "id"            => $this->id,        
            "name"          => $this->name,
            "descriptions"  => $this->descriptions,
            "style"         => $this->booth,
            "limit"         => $this->limit,
            "remaining"     => $this->limit,
          	"max"           => $this->max,
            "tickets"       => PublicTicketResource::collection($this->whenLoaded("tickets")),
        ];
    }
}



