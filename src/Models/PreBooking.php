<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class PreBooking extends Model {


    protected $table = "bob_prebooking";

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [

        'ticketdata' => 'array',
    ];


    public function event(){
        return $this->belongsTo(Event::class);
    }


    public function ticket(){
        return $this->belongsTo(Ticket::class);
    }



}
