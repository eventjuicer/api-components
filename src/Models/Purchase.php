<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
	
   	protected $table = "bob_purchases";


    public $timestamps = false;



	public function organizer()
    {
        return $this->belongsTo('Models\Organizer');
    }


	public function group()
    {
        return $this->belongsTo('Models\Group');
    }


   	public function event()
    {
        return $this->belongsTo('Models\Event');
    }

    public function participant()
    {
        return $this->belongsTo('Models\Participant');
    }

    public function fields()
    {
    	return $this->hasMany("Models\Field");
    }

    // public function tickets()
    // {
    //     //return $this->hasMany("Models\PurchaseTickets");

    //     return $this->belongsToMany('Models\Ticket', 'bob_participant_ticket', 'purchase_id', 'ticket_id');
    // }

    public function tickets()
    {
        return $this->hasMany("Models\PurchaseTicket", "participant_id");
    }


    public function getFinalPrice()
    {
        return $this->amount -  $this->discount ;
    }

    public function test()
    {
    	return 111;
    }

}
