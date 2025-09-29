<?php

namespace Eventjuicer\Resources\ExtApi;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
 

class ParticipantTicketResource extends Resource
{

    public function toArray($request)
    {   


        $data = [];
        $data["brand"] = isset($this->formdata["exhibitor"]) ? $this->formdata["exhibitor"] : [];
        $data["quantity"] = (int) $this->quantity;
        $data["ticket_id"] = (int) $this->ticket_id;
 		$data["created_at"] = (string) Carbon::createFromTimestamp($this->purchase->createdon);
        $data["amount"] = (int) $this->purchase->amount;
        $data["online"] = intval( $this->purchase->status_source === "payment");
        $data["settled"] = intval( $this->purchase->status === "ok");

        return $data;
    }
}



