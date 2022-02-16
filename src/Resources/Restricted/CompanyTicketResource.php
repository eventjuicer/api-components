<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Resources\PurchaseResource;


class CompanyTicketResource extends Resource
{



    public function toArray($request)
    {       

        return [
            "id"    => (int) $this->id,
            
           

            "organizer_id" => (int) $this->organizer_id,
            "group_id" => (int) $this->group_id,
            "event_id" => (int) $this->event_id,

            "ticket_group_id"    => (int) $this->ticket_group_id,
            // "ticket_group" => new AdminTicketGroupResource($this->group),

            "translation_asset_id" => (string) $this->translation_asset_id,
            "baseprice" => (int) $this->baseprice,
            "_price"  => $this->price,
            "price_currency" => (string) $this->price_currency,
            "_name"  => (string) array_get($this->names, "pl"),
            "names"  => $this->names,

         
            "delayed" 	=> (int) $this->delayed,
            "featured" 	=> (int) $this->featured,

            "role" => $this->role,

            // "tags" =>  AdminTagResource::collection($this->oldtags),

            "start" => (string) $this->start,
            "end" => (string) $this->end,
            "limit" => $this->limit,


            "thumbnail" => (string) $this->thumbnail,
            "image" => (string) $this->image,
            "details_url" => (string) $this->details_url,



        ];

    }


}



