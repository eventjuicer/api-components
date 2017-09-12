<?php


namespace Models;

use Illuminate\Database\Eloquent\Model;

use Models\Participant;
use Models\Organizer;
use Models\Group;
use Models\Event;


class TicketDownload extends Model
{

    protected $table = "eventjuicer_ticketdownloads";

    protected $guarded = ['id'];
  
    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function owner()
    {
        return $this->belongsTo(Participant::class);
    }

  
  

}
