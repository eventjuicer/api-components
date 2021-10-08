<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;

use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;


class VisitorResource extends Resource
{

    protected $presenterFields = ["fname", "lname", "cname2", "position", "nip"];

    public function toArray($request)
    {

            $data = [];

            $data["profile"]=  $this->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
            {     

                $value = $item->pivot->field_value;

                // if($item->name == "phone")
                // {
                //     $value = (new Phone($value))->obfuscated();
                // }

                return [ $item->name => $value ] ;

            })->all();

            $data["id"] = (int) $this->id;
            $data["ns"] = "participant";
            $data["domain"] = (new EmailAddress($this->email))->domain();


           return $data;
    }
}



