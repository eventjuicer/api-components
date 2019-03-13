<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;
 

class ParticipantDelivery extends Model
{

    protected $table = "bob_participant_deliveries";


    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }


    public function event()
    {
        return $this->belongsTo(Event::class);
    }

 

}
