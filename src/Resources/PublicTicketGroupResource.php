<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 

class PublicTicketGroupResource extends Resource
{

    public function toArray($request)
    {       
   
        $data = [

            "id"                => $this->id,        
            "name"              => $this->name,
            "descriptions"      => $this->descriptions,
            "map"               => $this->booth,
            "limit"             => $this->limit,

            "agg" => isset($this->agg) ? $this->agg  : [],
            
            "tickets"           => PublicTicketResource::collection($this->whenLoaded("tickets")),
        ];

        $data["map"]["width"] = isset($data["map"]["width"]) ? intval($data["map"]["width"]) : 0;

        return $data;

    }
}



