<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;


class TicketResource extends Resource
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
        "ticket_group_id"    => (int) $this->ticket_group_id,
        "translation_asset_id" => (string) $this->translation_asset_id,
        "internal_name" => (string) $this->internal_name,
        "baseprice" => (int) $this->baseprice,
        "_price"  => $this->price,
        "price_currency" => (string) $this->price_currency,
        "_name"  => (string) array_get($this->names, "pl"),
        "role" => $this->role,
        "start" => (string) $this->start,
        "end" => (string) $this->end,
        "limit" => $this->limit,

        ];  
    }
}
