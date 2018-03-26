<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;
use Eventjuicer\Services\Hashids;


class PresenterResource extends Resource
{

    protected $presenterFields = [

        "fname", 
        "lname", 
        "cname2", 
        "position", 
        "presentation_title", 
        "presentation_description",
        "presentation_venue",
        "presentation_time",
        "avatar",
        "bio"
    ];


    public function toArray($request)
    {




            $profile = $this->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
            {     
                return [ $item->name => $item->pivot->field_value ] ;

            })->all();

            $data = array_merge(array_fill_keys($this->presenterFields, ""), $profile);

            $data["id"] = (int) $this->id;
            $data["ns"] = "presenter";
            
    
           return $data;
    }
}



