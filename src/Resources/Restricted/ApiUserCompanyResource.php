<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;

use Eventjuicer\Models\Group;
use Eventjuicer\Models\Event;


/*
    "company": {
    "id": 1,
    "organizer_id": 1,
    "group_id": 1,
    "assigned_by": 1,
    "meetup_limit": 0,
    "slug": "unifiedfactory",
    "password": "",
    "assigned_at": "2017-12-29 12:49:49",
    "created_at": null,
    "updated_at": null
}

*/

class ApiUserCompanyResource extends Resource
{

    public function toArray($request)
    {   
        
        $active_event_id =  Group::find($this->group_id)->active_event_id;

        $active_event = Event::find($active_event_id);

        return [

            "id" => $this->id,
            "active_event" => new ApiUserCompanyEventResource($active_event),
            "name" => $this->name ?? $this->slug,
            "slug" => $this->slug,
            "meetup_limit" => $this->meetup_limit,
            "assigned_at" => (string) $this->assigned_at,
            "has_password" => intval( strlen($this->password) === 40),
          //  "users" => ApiUserCompanyUserResource::collection($this->participants)
        ];
    }
}



