<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;


class PublicFeaturedParticipant extends Resource
{

    protected $presenterFields = [
        "fname", 
        "cname2", 
        "position", 
        "logotype"
    ];


    public function toArray($request)
    {

            $data = array_fill_keys($this->presenterFields, "");

            $profile = $this->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
            {     
                return [ $item->name => $item->pivot->field_value ] ;

            })->all();

            $data = array_merge($data, $profile);

            $data["id"] = (int) $this->id;
   
           return $data;
    }
}



