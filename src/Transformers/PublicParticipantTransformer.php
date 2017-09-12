<?php

namespace Eventjuicer\Transformers;

use League\Fractal\TransformerAbstract;

use Eventjuicer\Models\Participant;

use Eventjuicer\ValueObjects\EmailAddress;


class PublicParticipantTransformer extends TransformerAbstract
{


    protected $availableIncludes = [
        'profileX'
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
            "email" => (new EmailAddress($participant->email))->obfuscated(),
            "lang" => $participant->lang, 
            "profile" => $participant->fields->pluck("field_value")->toArray(),
            "code" => hashids_encode( $participant->id )
        ];
    }

   


    public function includeProfile(Participant $participant)
    {
        $fields = $participant->fields->pluck("field_value")->toArray();

        return $this->item($fields, new ProfileTransformer);
    }

}
