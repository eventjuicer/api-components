<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Resources\PurchaseResource;


class CompanyTicketResource extends Resource
{



    public function toArray($request)
    {       

        $data = [];
        
        $data["id"] = $this->id;
        
        $data["names"] = $this->names;
        $data["price"] = $this->price;
        $data["start"] = (string)  $this->start;
        $data["end"] = (string) $this->end;

        return $data;

    }


}



