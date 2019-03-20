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
       
        $data["names"] = $this->ticket->names;
        $data["price"] = $this->ticket->price;
        $data["start"] = (string)  $this->ticket->start;
        $data["end"] = (string) $this->ticket->end;

        return $data;

    }


}



