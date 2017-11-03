<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;
use Eventjuicer\Services\Hashids;


class PublicVisitor extends Resource
{

    protected $presenterFields = ["fname", "lname", "cname2", "position, phone"];


    public function toArray($request)
    {


            $data = $this->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
            {     
                return [ $item->name => $item->pivot->field_value ] ;

            })->all();

            $data["id"] = (int) $this->id;
            $data["ns"] = "participant";
            $data["email"] = (new EmailAddress($this->email))->obfuscated();
            $data["code"] = (new Hashids())->encode($this->id);

           return $data;
    }
}



