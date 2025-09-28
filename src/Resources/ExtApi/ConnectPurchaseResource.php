<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;

class ConnectPurchaseResource extends Resource {


    public function toArray($request){
    
        $data = [];
        $data["id"] = $this->id;
        $data["amount"] = (int) $this->amount;
        $data["paid"] = (int) $this->paid;
        $data["payable"] = $this->status !== "cancelled" && !$this->paid && $this->amount > 0;
        $data["created_at"] = (string) Carbon::createFromTimestamp($this->createdon);
        $data["ticket_ids"] = $this->tickets->pluck("id")->all();
        return $data;

    }
}