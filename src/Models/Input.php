<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class Input extends Model
{
    protected $table = "bob_fields";

    public function participantfields(){
    	
    	return $this->hasMany(ParticipantFields::class);
    }

    
}
