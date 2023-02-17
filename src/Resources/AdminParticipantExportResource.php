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

            "going" => !is_null($this->ticketdownload) ? (int) $this->ticketdownload->going: "N/A",

            "code" => (new Hashids)->encode($this->id),

            "important" => (int) $this->important,

            "profile" => $this->fields->mapWithKeys(function($_item){
                
                return [$_item->name => $_item->pivot->field_value];
            }),

            // "ticket_ids" => $this->ticketpivot->filter(function($item){
            //     return $item->sold;
            // })->pluck("ticket_id")->all(),

            "created_at" => $this->createdon

        ];




    }
}

