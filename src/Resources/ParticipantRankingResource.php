<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

//use Eventjuicer\Models\Participant;

use Eventjuicer\ValueObjects\EmailAddress;



class ParticipantRankingResource extends Resource
{

    protected $presenterFields = ["fname", "lname", "cname2", "phone"];


    public function toArray($request)
    {

		//$data = parent::toArray($request);

		$data = $this->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
        {     
            return [ $item->name => $item->pivot->field_value] ;

        })->all();

        $data["id"] = $this->id;

        $data["email"] = (new EmailAddress($this->email))->obfuscated();

        $data["phone"] = isset($data["phone"]) ? 
            str_pad(substr($data["phone"], -4), 9, "x", STR_PAD_LEFT) : "";

		return $data;
            
    }
}



