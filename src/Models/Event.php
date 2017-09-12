<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\AbleTrait;

class Event extends Model
{

    use AbleTrait;

    protected $table = "bob_events";
    public $timestamps = false;

    protected $guarded = [
        "group_id", "organizer_id"
    ];


    public function tickets()
    {
    	return $this->hasMany("Models\Ticket", "event_id");
    }
      
    public function purchases()
    {
    	return $this->hasMany("Models\Purchase", "event_id");
    }

    public function participants()
    {
        return $this->hasMany("Models\Participant", "event_id");
    }

    public function group()
    {
    	return $this->belongsTo("Models\Group");

    }

 	public function organizer()
    {
    	return $this->belongsTo("Models\Organizer");
    }


   



}
