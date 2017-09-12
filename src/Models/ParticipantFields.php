<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class ParticipantFields extends Model
{
    protected $table = "bob_participant_fields";

 	protected $hidden = ['organizer_id', 'group_id', "event_id"];

    public $timestamps = false;


    public function participant()
    {
    	return $this->belongsTo("Models\Participant");
    }

    public function purchase()
    {
    	return $this->belongsTo("Models\Purchase");
    }

    public function input()
    {
    	return $this->belongsTo("Models\Input");
    }

    public function field2()
    {
    	dd($this->input());
    }
}
