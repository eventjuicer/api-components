<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 

class AdminParticipantResource extends Resource
{

    public function toArray($request)
    {

        return [

            "id" => $this->id,
            
            "email" => $this->email,

            "ticket_ids" => $this->ticketpivot->filter(function($item){
                return $item->sold;
            })->pluck("ticket_id")->all(),

            "created_at" => $this->createdon

        ];

            
    }
}



