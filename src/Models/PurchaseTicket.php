<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseTicket extends Model
{
    protected $table = "bob_participant_ticket";

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

  




}
