<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 

class AdminParticipantResource extends Resource
{

    public function toArray($request)
    {

        $profile = $this->profile();


        return [



            "id" => $this->id,
            
            "email" => $this->email,

            "profile" => $profile,

            "important" => $this->important || !empty($profile["important"]),

            "going" => $this->ticketdownload ? $this->ticketdownload->going: null,

            "ticket_ids" => $this->ticketpivot->filter(function($item){
                return $item->sold;
            })->pluck("ticket_id")->all(),

            "created_at" => $this->createdon

        ];




    }
}



