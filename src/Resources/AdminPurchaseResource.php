<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

use Carbon\Carbon;


class AdminPurchaseResource extends Resource
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

           "participant_id" => $this->participant_id,

           "company_id" => $this->participant->company_id,
         
           "email" => $this->participant->email,
                
           "paid" => $this->paid,

           "amount" => $this->amount,
           "discount" => $this->discount,
           "total" => $this->amount - $this->discount,

           "status" => $this->status,
           "status_source" => $this->status_source,
           "created_at" => (string) Carbon::createFromTimestamp($this->createdon),
           "updated_at" => $this->updatedon,

        //   "tickets" =>  TicketResource::collection($this->whenLoaded("tickets")) 
        ];  
    }
}
