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

            $name   = $item->name;
            $value  = $item->pivot->field_value;

            switch($name)
            {
                case "phone":
                
                    $value = $this->mask( str_replace(" ", "", $value) );

                break;

                case "lname":

                    $value = $this->mask($value);

                break;
            }

            return [ $name => $value ];

        })->all();

        $data["id"] = $this->id;

        $data["email"] = (new EmailAddress($this->email))->obfuscated("*");

		return $data;
            
    }


    protected function mask($str, $maskWith = "*")
    {
        $strlen = mb_strlen($str);
        $mask   = round($strlen / 2);

        return str_pad( 
                    mb_substr( $str , -1 * $mask), 
                $strlen, $maskWith, STR_PAD_LEFT);
    }


}



