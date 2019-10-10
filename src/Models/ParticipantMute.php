<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;
 

class ParticipantMute extends Model
{


    protected $table = "bob_participant_mutes";

    protected $fillable = ["email", "event_id", "group_id", "organizer_id", "level"];


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
