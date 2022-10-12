<?php

namespace Eventjuicer\Resources\Restricted;
use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\ValueObjects\EmailAddress;

class FullVisitorResource extends Resource {

    protected $presenterFields = ["fname", "lname", "phone", "cname2", "position", "nip", "important"];

    public function toArray($request){

            $data = [];

            $data["profile"]=  $this->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item){     

                $value = $item->pivot->field_value;

                return [ $item->name => $value ] ;

            })->all();

            $data["id"] = (int) $this->id;
            $data["email"] =  $this->email;
            $data["important"] =  (int) $this->important;
            $data["ns"] = "participant";
            $data["domain"] = (new EmailAddress($this->email))->domain();

           return $data;
    }
}



