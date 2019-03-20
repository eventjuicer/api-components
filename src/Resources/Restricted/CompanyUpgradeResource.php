<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Resources\PurchaseResource;


class CompanyUpgradeResource extends Resource
{



    public function toArray($request)
    {       

        $data = [];
        
        $data["id"] = $this->id;
        $data["ticket_id"] = $this->id;

        $data["event_id"] = $this->event_id;
        $data["names"] = $this->names;
        $data["price"] = $this->price;
        
        //these are bare ticket info so we do not actually need it!
        // $data["limit"] = $this->limit;
        // $data["max_quantity"] = $this->max;
        
        $data["role"] = (string) $this->role;
        
        $data["start"] = (string)  $this->start;
        $data["end"] = (string) $this->end;
        $data["change"] = (string) $this->change;

        $data["changeable"] = $this->changeable;

        $data["in_dates"] = $this->in_dates;

        $data["remaining"] = $this->remaining;
        $data["bookable"] = $this->bookable;
        $data["booked"] = $this->booked;
        $data["unpaid"] = $this->unpaid;

        $data["thumbnail"] = $this->thumbnail;
        $data["image"] = $this->image;

        $data["purchase_ids"] = $this->transactions->pluck("purchase_id");

      //  $data["transactions"] = CompanyUpgradePurchaseResource::collection($this->transactions);
        
        return $data;

    }


}



