<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

//use Eventjuicer\Models\Participant;

use Eventjuicer\ValueObjects\EmailAddress;
use Eventjuicer\ValueObjects\Lname;
use Eventjuicer\ValueObjects\Phone;



class ParticipantRankingResource extends Resource
{

    protected $presenterFields = ["fname", "lname", "cname2", "phone"];


    public function toArray($request)
    {

		//$data = parent::toArray($request);

		$data = $this->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
        {     

            $name   = $item->name;
            $value  = $item->pivot->field_value;

            switch($name)
            {
                case "phone":
                
                    $value =  (new Phone($value))->obfuscated("*");

                break;

                case "lname":

                    $value = (new Lname($value))->obfuscated("*");

                break;
            }

            return [ $name => $value ];

        })->all();

        $data["id"] = $this->id;

        $data["email"] = (new EmailAddress($this->email))->obfuscated("*");

		return $data;
            
    }


    

}



