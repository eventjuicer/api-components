<?php

namespace Transformers;

use League\Fractal\TransformerAbstract;

use Models\Participant;

use Services\Firebase;

class ExtAuthTransformer extends TransformerAbstract
{

    protected $exhibitorFields = ["cname2", "booth", "logotype", "company_website"];

    protected $firebase;

    function __construct(Firebase $firebase)
    {
        $this->firebase = $firebase;
    }


    public function transform(Participant $participant)
    {

        return $participant->fields->whereIn("name", $this->exhibitorFields)->mapWithKeys(function($item)
            {
                
                return [ $item->name => $item->pivot->field_value ];

            })->put("participant_id", (int) $participant->id)->put("jwt", $this->jwt($participant))->toArray();
       
    }


    private function jwt($participant)
    {
        return $this->firebase->create_custom_token($participant, ["premium" => false]);
    }





}