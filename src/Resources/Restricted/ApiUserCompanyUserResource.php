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

class ApiUserCompanyUserResource extends Resource
{


    protected $visible = ["fname", "lname", "phone"];


    public function toArray($request)
    {   
        return [

            "id" => $this->id,
            "email" => $this->email,
            "profile" => $this->remapFields($this->fields)
           
        ];
    }

    protected function remapFields($fields)
    {
           return $fields->whereIn("name", $this->visible)->mapWithKeys(function($item)
            {     

                $value = $item->pivot->field_value;

                return [ $item->name => $value ] ;

            })->all();
    }

}



