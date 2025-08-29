<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Resources\PublicTicketResource;
use Carbon\Carbon;


class PurchaseResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
       return [
        
        "id"    => (int) $this->id,
        "amount" => $this->amount * 100,
        "currency" => "PLN",
        "paid" => $this->paid,
        "status" => $this->status,
        "status_source" => $this->status_source,
        "created_at" => (string) Carbon::createFromTimestamp($this->createdon),
        "updated_at" => $this->updatedon,
        "tickets" => TicketPivotResource::collection($this->ticketpivot)
        ];  
    }
}
