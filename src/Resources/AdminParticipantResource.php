<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Hashids;

class AdminParticipantResource extends Resource
{

    public function toArray($request)
    {

        $profile = $this->profile();


        return [



            "id" => $this->id,
            
            "email" => $this->email,

            "profile" => [
                "fname" => $profile["fname"] ?? "",
                "lname" => $profile["lname"] ?? "",
                "cname2" => $profile["cname2"] ?? "",
                "position" => $profile["position"] ?? "",
                "phone" => $profile["phone"] ?? "",
                "url" => $profile["url"] ?? "",
                "referral" => $profile["referral"] ?? "",
                "booth" => $profile["booth"] ?? "",

            ],

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



