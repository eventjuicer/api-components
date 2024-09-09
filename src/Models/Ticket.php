<?php


/*

    id 
    portal_id
    organizer_id
    admin_id       
    parent_id      
    is_published       
    is_sticky       
    is_promoted       
    is_coverstory     
    is_deleted    
    interactivity   
    createdon    
    updatedon      
    publishedon     
    editedby

*/  


namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Models\Traits\AbleTrait;

class Ticket extends Model{

    use AbleTrait;

    protected $table = "bob_tickets";
    public $timestamps = false;

    protected $casts = [

        'price' => 'array',
        'names' => 'array',
        'descriptions' => 'array',
        // "json"  => "array"
    ];

    protected $dates = ['start', 'end'];

   // public function pu(){
   //      return $this->hasManyThrough(
   //          'Models\Participant', 'Models\Participant',
   //          'id', 'user_id', 'id'
   //      );
   //}

    public function purchasesNotCancelled()
    {
        
        return $this->belongsToMany(Purchase::class, 'bob_participant_ticket', 'ticket_id', 'purchase_id')->wherePivot("sold", 1);

    }

    public function participantsNotCancelled()
    {
        
        return $this->belongsToMany(Participant::class, 'bob_participant_ticket', 'ticket_id', 'participant_id')->wherePivot("sold", 1)->orderBy("participant_id", "DESC");

    }



    public function oldtags()
    {
        
        return $this->belongsToMany(Tag::class, 'bob_taggings', 'object_id', 'tag_id')->wherePivot("object_name", "ticket")->withPivot("createdon");

    }

    public function fields(){
        
        return $this->belongsToMany(Field::class, 'bob_fieldsets')->withPivot("event_id", "type", "sorting");

    }
  
    public function contexts()
    {
        return $this->morphToMany(Context::class, 'contextable');
    }


    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    
    public function ticketpivot()
    {
        
        return $this->hasMany(ParticipantTicket::class, "ticket_id");

    }

    public function group()
    {
        
        return $this->belongsTo(TicketGroup::class, "ticket_group_id");

    }
  

}
