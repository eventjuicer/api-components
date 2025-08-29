<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;


class TicketPivotResource extends Resource
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
        
        "participant_id"    => (int) $this->participant_id,
        "purchase_id" => (int) $this->purchase_id,
        "ticket_id" => (int) $this->ticket_id,
        "event_id" => (int) $this->event_id,
        "quantity" => (int) $this->quantity,
        "sold" => (int) $this->sold,
        "formdata" => $this->formdata,
        "ticket" =>  new TicketResource($this->whenLoaded("ticket")) 
        ];  
    }
}
