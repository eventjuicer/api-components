<?php

namespace Transformers;

use League\Fractal\TransformerAbstract;

use Models\Participant;

class ParticipantTransformer extends TransformerAbstract
{

    public static $obfuscate = false;


    protected $availableIncludes = [
        'purchases', 'profile'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Participant $participant)
    {
        return [
            "id" => (int) $participant->id,
            "email" => self::$obfuscate ? $this->emailObfuscator($participant->email) : $participant->email,
            "created_at" => $participant->createdon,
            "lang" => $participant->lang 
        ];
    }

    protected function emailObfuscator($email)
    {
        $len = strlen($email);

        return substr($email, 0, 3) . "...@..." .  substr($email, -3);
    }

    public function includePurchases(Participant $participant)
    {
        return $this->collection($participant->purchases, new PurchaseTransformer);
    }

    public function includeProfile(Participant $participant)
    {
        $fields = $participant->fields->pluck("field_value")->toArray();

        return $this->item($fields, new ProfileTransformer);
    }

}
