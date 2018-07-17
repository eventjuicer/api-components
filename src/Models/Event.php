<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Models\Traits\AbleTrait;

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
    	return $this->hasMany(Ticket::class, "event_id");
    }
      
    public function purchases()
    {
    	return $this->hasMany(Purchase::class, "event_id");
    }

    public function participants()
    {
        return $this->hasMany(Participant::class, "event_id");
    }

    public function group()
    {
    	return $this->belongsTo(Group::class);

    }

 	public function organizer()
    {
    	return $this->belongsTo(Organizer::class);
    }


   



}
