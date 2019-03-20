<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Resources\PurchaseResource;


class CompanyPurchaseResource extends Resource
{

    public function toArray($request)
    {       

        $data = [];
        
        $data["id"]         = $this->id;

        $data["event_id"]   = $this->event_id;
        $data["event_name"] = $this->event->names;

        $data["ticket_ids"] = $this->ticketpivot->pluck("ticket_id");

        $data["tickets"]    = CompanyTicketPivotResource::collection($this->ticketpivot);

        $data["buyer_id"]  = $this->participant_id;
        $data["buyer_email"] = $this->participant->email;
        $data["company_id"]  = $this->participant->company_id;


        $data["status"]     = $this->status;
        $data["amount"]     = number_format($this->amount - $this->discount, 2);

        $data["finalized"]  = $this->paid;
        $data["ts"]         = (string) $this->updatedon;
        
        return $data;

    }


}



