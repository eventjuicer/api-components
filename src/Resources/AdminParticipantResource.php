<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Hashids;
use Eventjuicer\ValueObjects\Url;


class AdminParticipantResource extends Resource
{

    public function toArray($request){

        $profile = $this->profile();
        $url = new Url($profile["url"] ?? "");

        return [



            "id" => $this->id,
            
            "email" => $this->email,

            "profile" => $profile,

            "utms" =>   $url->utms(),
            "path" =>  $url->path(),

            "important" => $this->important || !empty($profile["important"]),

            "going" => $this->ticketdownload ? $this->ticketdownload->going: null,

            "code" => (new Hashids)->encode($this->id),

            "ticket_ids" => $this->ticketpivot->filter(function($item){
                return $item->sold;
            })->pluck("ticket_id")->all(),

            "created_at" => $this->createdon

        ];




    }
}



