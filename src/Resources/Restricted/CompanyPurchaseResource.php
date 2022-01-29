<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Resources\PurchaseResource;
use Eventjuicer\Resources\PublicEventResource;

class CompanyPurchaseResource extends Resource
{

    public function toArray($request)
    {       

        $data = [];
        
        $data["id"]         = $this->id;

        $data["group_id"]   = $this->group_id;
        $data["event_id"]   = $this->event_id;
        $data["participant_id"]   = $this->participant_id;

        $data["status"]     = $this->status;
        $data["amount"]     = max(0, intval($this->amount) - intval($this->discount) );
        $data["locale"]     = $this->locale; 
        $data["finalized"]  = $this->paid;
        $data["ts"]         = (string) $this->updatedon;
        
        $data["buyer_email"] = $this->participant->email;
        $data["company_id"]  = $this->participant->company_id;
        $data["ticket_ids"] = $this->ticketpivot->pluck("ticket_id");
        $data["event"] = new PublicEventResource($this->event);
        $data["tickets"]    = CompanyTicketPivotResource::collection($this->ticketpivot);


        return $data;

    }


}



