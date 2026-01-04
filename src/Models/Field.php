<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $table = "bob_fields";

    protected $casts = array(

    	"options" => "array"
    );


    public function participants(){
        
        return $this->belongsToMany(Participant::class, 'bob_participant_fields', 'field_id', 'participant_id');

    }

    public function fields(){
        
        return $this->belongsToMany(Field::class, 'bob_fieldsets')->withPivot("event_id", "type", "sorting");

    }
}
