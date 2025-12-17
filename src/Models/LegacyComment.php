<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyComment extends Model
{
    protected $table = "bob_newsdesk_comments";

    
    public $timestamps = false;

	public function organizer(){
        return $this->belongsTo(Organizer::class);
    }

	public function group(){
        return $this->belongsTo(Group::class);
    }

   	public function event(){
        return $this->belongsTo(Event::class);
    }

    public function participant(){
        return $this->belongsTo(Participant::class);
    }











}