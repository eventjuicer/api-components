<?php

namespace Transformers;

use League\Fractal\TransformerAbstract;

use Models\Participant;



use ValueObjects\EmailAddress;

class PublicVisitorTransformer extends TransformerAbstract
{


    protected $presenterFields = ["fname", "lname", "cname2"];

    function __construct(  )
    {
    }

    public function transform($participant)
    {

    //    dd($participant->fields->toArray());

            return $participant->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
            {     
                return [ $item->name => $item->pivot->field_value] ;

            })->put("participant_id", (int) $participant->id)->put("email", (string) (new EmailAddress($participant->email))->obfuscated() )->put("code", hashids_encode( $participant->id ) )->toArray();
    }


}
