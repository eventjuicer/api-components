<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Hashids;
use Eventjuicer\Services\Traits\Fields;


class AdminParticipantExportResource extends Resource{

    use Fields;


    protected $showable = array(
        
        "fname",
        "lname",
        "cname2",
        "position",
        "phone",

        "referral",
        "url"
    ); 


    public function toArray($request)
    {


        return [


            "id" => $this->id,
            
            "email" => $this->email,

            "is_going" => !is_null($this->ticketdownload) ? (int) $this->ticketdownload->going: "N/A",

            "code" => (new Hashids)->encode($this->id),

            "is_vip" => (int) $this->important,

            "profile" => $this->filterFields($this->fieldpivot, $this->showable),

            // "ticket_ids" => $this->ticketpivot->filter(function($item){
            //     return $item->sold;
            // })->pluck("ticket_id")->all(),

            "created_at" => $this->createdon

        ];




    }
}

