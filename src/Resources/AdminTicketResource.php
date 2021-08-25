<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AdminTicketResource extends Resource
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
            
           

            "organizer_id" => (int) $this->organizer_id,
            "group_id" => (int) $this->group_id,
            "event_id" => (int) $this->event_id,

            "ticket_group_id"    => (int) $this->ticket_group_id,
            "ticket_group" => new AdminTicketGroupResource($this->group),


            "translation_asset_id" => (string) $this->translation_asset_id,
            "internal_name" => (string) $this->internal_name,
            "baseprice" => (int) $this->baseprice,
            "_price"  => $this->price,
            "price_currency" => (string) $this->price_currency,
            "_name"  => (string) array_get($this->names, "pl"),

            "role" => $this->role,

            "tags" =>  AdminTagResource::collection($this->oldtags),

            "start" => (string) $this->start,
            "end" => (string) $this->end,
            "limit" => $this->limit,
            "agg" =>  $this->agg,
            "remaining" => $this->remaining,

            "thumbnail" => (string) $this->thumbnail,
            "image" => (string) $this->image,
            "details_url" => (string) $this->details_url,

            "in_dates" => $this->in_dates,
            "bookable" => $this->bookable,
            "errors" => $this->errors,
            "status" => $this->status

        ];
    }
}




