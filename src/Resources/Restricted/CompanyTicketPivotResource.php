<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Resources\PurchaseResource;


class CompanyTicketPivotResource extends Resource
{



    public function toArray($request)
    {       

        $data = [];
        
        $data["id"] = $this->ticket_id;
        $data["quantity"] = $this->quantity;
       
        $data["names"] = $this->ticket ? $this->ticket->names : [];
        $data["price"] = $this->ticket ? $this->ticket->price : [];
        $data["start"] = $this->ticket? (string)  $this->ticket->start : "";
        $data["end"] = $this->ticket ? (string) $this->ticket->end : "";
        $data["role"] = $this->ticket ? (string) $this->ticket->role : "";

        return $data;

    }


}



