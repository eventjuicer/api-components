<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;


class PublicVisitor extends Resource
{

    protected $presenterFields = ["fname", "lname", "cname2", "position"];


    public function toArray($request)
    {

            return $this->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
            {     
                return [ $item->name => $item->pivot->field_value] ;

            })->put("id", (int) $this->id)->put("ns", "participant")->put("email", (string) (new EmailAddress($this->email))->obfuscated() )->put("code", hashids_encode( $this->id ) )->toArray();
    }
}



