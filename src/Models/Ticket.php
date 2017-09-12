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
namespace Models;

use Illuminate\Database\Eloquent\Model;

use Services\AbleTrait;


class Ticket extends Model
{


  use AbleTrait;


    protected $table = "bob_tickets";


   //  public function pu()
   //  {
   //      return $this->hasManyThrough(
   //          'Models\Participant', 'Models\Participant',
   //          'id', 'user_id', 'id'
   //      );
   
   // }

    public function purchasesNotCancelled()
    {
        
        return $this->belongsToMany('Models\Purchase', 'bob_participant_ticket', 'ticket_id', 'purchase_id')->wherePivot("sold", 1);

    }

    public function participantsNotCancelled()
    {
        
        return $this->belongsToMany('Models\Participant', 'bob_participant_ticket', 'ticket_id', 'participant_id')->wherePivot("sold", 1);

    }

  
    public function contexts()
    {
        return $this->morphToMany('Models\Context', 'contextable');
    }


    public function event()
    {
        return $this->belongsTo('Models\Event');
    }

  
  

}
