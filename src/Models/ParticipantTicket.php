<?php

   
namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;


class ParticipantTicket extends Model
{



    protected $table = "bob_participant_ticket";

    protected $casts = [

        'formdata' => 'array',
    ];


    protected $dates = ["updatedon"];

    public $timestamps = false;



    // public function purchasesNotCancelled()
    // {
        
    //     return $this->belongsToMany(Purchase::class, 'bob_participant_ticket', 'ticket_id', 'purchase_id')->wherePivot("sold", 1);

    // }

    // public function participantsNotCancelled()
    // {
        
    //     return $this->belongsToMany(Participant::class, 'bob_participant_ticket', 'ticket_id', 'participant_id')->wherePivot("sold", 1)->orderBy("participant_id", "DESC");

    // }

  
    public function contexts()
    {
        return $this->morphToMany(Context::class, 'contextable');
    }


    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

  

}
