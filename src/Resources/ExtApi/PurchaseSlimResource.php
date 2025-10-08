<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;


class PurchaseSlimResource extends Resource
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
        "participant_id" => (int) $this->participant_id,
        "event_id" => (int) $this->event_id,
        "group_id" => (int) $this->group_id,
        "organizer_id" => (int) $this->organizer_id,
        "amount" => $this->amount * 100,
        "currency" => "PLN",
        "paid" => $this->paid,
        "invoice_id" => $this->invoice_id,
        "preinvoice_id" => $this->preinvoice_id,
        "invoice_sent_at" => $this->invoice_sent_at,
        "preinvoice_sent_at" => $this->preinvoice_sent_at,
        "status" => $this->status,
        "status_source" => $this->status_source,
        "created_at" => (string) Carbon::createFromTimestamp($this->createdon),
        "updated_at" => $this->updatedon,
        "tickets" => PurchaseParticipantTicketPivotResource::collection($this->tickets),
     
      
        ];  
    }
}
