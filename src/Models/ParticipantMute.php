<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;
 

class ParticipantMute extends Model
{


    protected $table = "bob_participant_mutes";


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
