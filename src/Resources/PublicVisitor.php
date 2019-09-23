<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;
use Eventjuicer\Services\Hashids;


class PublicVisitor extends Resource
{

    protected $presenterFields = ["fname", "cname2"];


    public function toArray($request)
    {

            if(!$this->fields){
                return [];
            }

            $profile = $this->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
            {     
                return [ $item->name => $item->pivot->field_value ] ;

            })->all();

            $data = array_merge(array_fill_keys($this->presenterFields, ""), $profile);


            $data["id"] = (int) $this->id;
            $data["ns"] = "participant";
            $data["email"] = (new EmailAddress($this->email))->obfuscated();
            $data["code"] = (new Hashids())->encode($this->id);

           return $data;
    }
}



