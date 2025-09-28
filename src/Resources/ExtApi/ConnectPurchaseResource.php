<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;

class ConnectPurchaseResource extends Resource {


    public function toArray($request){
    
        $data = [];
        $data["id"] = $this->id;
        $data["amount"] = $this->amount;
        $data["status"] = $this->status;
        $data["created_at"] = (string) Carbon::createFromTimestamp($this->createdon);
        $data["tickets"] = ConnectTicketResource::collection($this->tickets);
        return $data;

    }
}