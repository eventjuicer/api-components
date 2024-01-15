<?php

namespace Eventjuicer\Resources\Restricted;
use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\ValueObjects\EmailAddress;

class VisitorResource extends Resource {

    protected $presenterFields = [
        "fname", 
        "lname", 
        "cname2", 
        "position", 
        "nip", 
        "important", 
        "participant_type", 
        "profile_linkedin"
    ];

    public function toArray($request){

            $data = [];

            $data["profile" ]= $this->fields? $this->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item) {     

                $value = $item->pivot->field_value;

                return [ $item->name => $value ] ;

            })->all(): [];

            $data["id"] = (int) $this->id;
            // $data["important"] = (int) $this->important;
            // $data["ns"] = "participant";
            $data["domain"] = (new EmailAddress($this->email))->domain();


           return $data;
    }
}



