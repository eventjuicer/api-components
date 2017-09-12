<?php

namespace Transformers;

use League\Fractal\TransformerAbstract;

use Models\Participant;


class PublicPresenterTransformer extends TransformerAbstract
{

    protected $presenterFields = ["fname", "lname", "avatar", "logotype","presenter", "presentation_time", "presentation_title", "presentation_description", "presentation_venue", "cname2"];

    function __construct( )
    {
    }
 
    public function transform(Participant $participant)
    {
        return $participant->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
            {

                return [ $item->name => $item->pivot->field_value ];

            })->put("participant_id", (int) $participant->id)->toArray();
    }



}
