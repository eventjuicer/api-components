<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Resources\PurchaseResource;


class CompanyParticipantTicketResource extends Resource
{

    public function toArray($request)
    {       

        $data = [];
        
        $data["id"]         = $this->purchase->id;

        $data["event_id"]   = $this->event->id;
        $data["event_name"] = $this->event->names;

        $data["ticket_ids"] = $this->purchase->tickets->pluck("id");

     //   $data["tickets"]    = CompanyTicketPivotResource::collection($this->ticketpivot);

        $data["buyer_id"]  = $this->participant->id;
        $data["buyer_email"] = $this->participant->email;
        $data["company_id"]  = $this->participant->company_id;


        $data["status"]     = $this->purchase->status;
        $data["amount"]     = $this->purchase->amount;

        $data["finalized"]  = $this->purchase->paid;
        $data["ts"]         = (string) $this->purchase->updatedon;
        
        return $data;

    }


}



