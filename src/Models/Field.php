<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $table = "bob_fields";

    public function participants()
    {
        
        return $this->belongsToMany('Models\Participant', 'bob_participant_fields', 'field_id', 'participant_id');

    }
}
