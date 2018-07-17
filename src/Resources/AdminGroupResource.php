<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AdminGroupResource extends Resource
{

    static $groups;


    static public function setGroups($groups){

        self::$groups = $groups;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {


       return [

            "id"        => (int) $this->id,
            "name"      => $this->name,
            "is_portal" => $this->is_portal,
            "active_event_id" => $this->active_event_id,
            "active_event" => new AdminEventResource($this->activeEvent->first()),
            "events" => AdminEventResource::collection( $this->whenLoaded("latestEvents"))
               
        ];
    }
}
