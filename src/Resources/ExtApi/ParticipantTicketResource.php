<?php

namespace Eventjuicer\Resources\ExtApi;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
 

class ParticipantTicketResource extends Resource
{

    public function toArray($request)
    {   


        $data = [];
        $data["formdata"] = $this->formdata;
        $data["quantity"] = (int) $this->quantity;
        $data["ticket_id"] = (int) $this->ticket_id;
 		$data["created_at"] = (string) Carbon::createFromTimestamp($this->purchase->createdon);
        $data["amount"] = (int) $this->purchase->amount;
        $data["online"] = (int) $this->purchase->status_source == "payment";
        $data["settled"] = (int) $this->purchase->status == "ok";

        return $data;
    }
}



