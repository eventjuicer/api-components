<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;
use Eventjuicer\Models\Ticket;

class Purchase extends Model{
	
   	protected $table = "bob_purchases";

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

    public function fields(){
    	return $this->hasMany(Field::class);
    }

    public function ticketpivot(){
        return $this->hasMany(ParticipantTicket::class, "purchase_id");
    }

    public function tickets(){

        return $this->belongsToMany(Ticket::class, 'bob_participant_ticket', 'purchase_id', 'ticket_id')->withPivot("sold", "formdata", "quantity");
    }


    public function getFinalPrice(){
        return $this->amount -  $this->discount ;
    }


}
