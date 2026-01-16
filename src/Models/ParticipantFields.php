<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;


use Eventjuicer\Models\Participant;
use Eventjuicer\Models\Input;
use Eventjuicer\Models\Purchase;


class ParticipantFields extends Model
{
    protected $table = "bob_participant_fields";

 	protected $hidden = ['organizer_id', 'group_id', "event_id"];

    const CREATED_AT = null;
    const UPDATED_AT = "updatedon";
    // public $timestamps = false;

    protected $touches = ['participant'];

    protected $guarded = array();

    public function participant()
    {
    	return $this->belongsTo(Participant::class);
    }

    public function purchase()
    {
    	return $this->belongsTo(Purchase::class);
    }

    public function input()
    {
    	return $this->belongsTo(Input::class, "field_id");
    }

    public function field2()
    {
    	dd($this->input());
    }
}
