<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;



class CompanyUpgradeResource extends Resource
{



    public function toArray($request)
    {       

        $data = [];
        $data["id"] = $this->id;
        $data["event_id"] = $this->event_id;
        $data["names"] = $this->names;
        $data["price"] = $this->price;
        $data["limit"] = $this->limit;
        $data["max_quantity"] = $this->max;
        $data["role"] = $this->role;
        $data["start"] = (string)  $this->start;
        $data["end"] = (string) $this->end;
        $data["in_dates"] = $this->in_dates;
        $data["remaining"] = $this->remaining;
        $data["bookable"] = $this->bookable;
        $data["booked"] = $this->booked;
        
        $data["thumbnail"] = $this->thumbnail;
        $data["image"] = $this->image;
        
        return $data;

    }


}



