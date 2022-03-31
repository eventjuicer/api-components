<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;

class CompanyVipcodeVisitorResource extends Resource {

    protected $presenterFields = [
        "fname", 
        "lname", 
        "cname2", 
        "position", 
        "nip",
        "phone"
    ];

    public function toArray($request){

            $data = [];

            $data["profile"]=  $this->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item) {     
                return [ $item->name => $item->pivot->field_value ] ;
            })->all();

            $data["id"] = (int) $this->id;
            $data["email"] = $this->email;

           return $data;
    }
}



