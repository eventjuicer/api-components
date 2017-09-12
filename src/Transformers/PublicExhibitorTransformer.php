<?php

namespace Transformers;

use League\Fractal\TransformerAbstract;

use Models\Participant;


class PublicExhibitorTransformer extends TransformerAbstract
{

    protected $exhibitorFields = ["cname2", "booth", "logotype", "company_website"];

    function __construct()
    {

    }


    public function transform(Participant $participant)
    {

        return $participant->fields->whereIn("name", $this->exhibitorFields)->mapWithKeys(function($item)
            {
                
                return [ $item->name => $item->pivot->field_value ];

            })->put("participant_id", (int) $participant->id)->toArray();
       
    }





}