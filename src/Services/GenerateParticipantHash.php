<?php

namespace Eventjuicer\Services;

use Eventjuicer\Services\Hashids;
use Eventjuicer\Models\Participant;

class GenerateParticipantHash {

    private $participant;

    function __construct(Participant $participant)
    {
        $this->participant = $participant;
    }

    function __toString()
    {
        return  (new Hashids())->encode($this->participant->id); 
    }
}