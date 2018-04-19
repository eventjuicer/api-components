<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;




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



    protected $presenterFields = [

        "name",
        "about", 
        "products",
        "expo", 
        "keywords",
        "website",
        "facebook",
        "twitter",
        "linkedin",
        "logotype",
        "opengraph_image",
        "countries",
        "invitation_template",
        "event_manager"

    ];

    public function toArray($request)
    {   
        

        return [

            "id" => $this->id,
      
            "name" => $this->name ?? $this->slug,

            "slug" => $this->slug,
        
            "assigned_at" => (string) $this->assigned_at,
            
            "has_password" => intval( strlen($this->password) === 40),

            "profile"   =>  $this->data->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
            {     

                return [ $item->name => $item->value ] ;

            })->all(),

          //  "users" => ApiUserCompanyUserResource::collection($this->participants)
        ];
    }
}



