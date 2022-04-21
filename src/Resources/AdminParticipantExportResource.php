<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Hashids;

class AdminParticipantExportResource extends Resource
{

    public function toArray($request)
    {

        


        return [



            "id" => $this->id,
            
            "email" => $this->email,

            "going" => $this->ticketdownload ? (int) $this->ticketdownload->going: null,

            "code" => (new Hashids)->encode($this->id),

            "ticket_ids" => $this->ticketpivot->filter(function($item){
                return $item->sold;
            })->pluck("ticket_id")->all(),

            "created_at" => $this->createdon

        ];




    }
}



