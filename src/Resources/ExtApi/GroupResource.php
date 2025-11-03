<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;

class GroupResource extends Resource {

 
    public function toArray($request)
    {
       return [

            "id"        => (int) $this->id,
            "name" => $this->name,
            "active_event_id"      => (int) $this->active_event_id,
            "events" => EventResource::collection( $this->whenLoaded("latestEvents")),
           
        ];
    }
}
