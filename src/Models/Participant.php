<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Models\Traits\AbleTrait;

use Eventjuicer\Models\Traits\Elasticsearch;

use Eventjuicer\Repositories\ParticipantRepository;

class Participant extends Model
{


    use AbleTrait;
    use Elasticsearch;


    protected $table = "bob_participants";

 	protected $hidden = ["tickets"];

    protected $guarded = ["event_id", "group_id", "organizer_id"];


    public $timestamps = false;

    
    public function getRepository()
    {
        return ParticipantRepository::class;
    }




    public function contexts()
    {
        return $this->morphToMany('Models\Context', 'contextable');
    }



    public function personalize($str_with_profile_fields)
    {
        

    }


    public function profile($key = "", $replacement = "")
    {

        $profile = $this->fields->mapWithKeys(function($_item){
                
                return [$_item->name => $_item->pivot->field_value];
        });
        return !empty($key) ? $profile->get($key, $replacement) : $profile->all();    
    }



    /**NEW OR CHECKED**/
 
    

    public function ticketdownloads()
    {
        return $this->hasMany(TicketDownload:: class);
    }


    public function parent()
    {
        return $this->belongsTo(Participant::class, "parent_id");
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function children()
    {
        return $this->hasMany(Participant::class, "parent_id");
    }

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

    public function tickets()
    {
        
        return $this->belongsToMany(Ticket::class, 'bob_participant_ticket', 'participant_id', 'ticket_id')->withPivot("purchase_id", "sold");

    }

    public function fields()
    {
        
        return $this->belongsToMany(Field::class, 'bob_participant_fields', 'participant_id', 'field_id')->withPivot("field_value","participant_id");//->withTimestamps();//->wherePivot("sold", 1);

    }

    public function ticketpivot()
    {
        
        return $this->hasMany(ParticipantTicket::class, "participant_id");

    }


    /**NEW OR CHECKED**/






    public function scans()
    {
        return $this->hasMany(Scan::class, "owner_id")->orderby("id", "DESC");
    }

    public function scannedParticipants()
    {
        
        return $this->belongsToMany(Participant::class, 'eventjuicer_barcode_scans', 'owner_id', 'scanned_id')->withPivot("scanned_at");

    }



    public function ScanOwners()
    {
        return $this->hasMany(Scan::class, "owner_id");
    }

    
 



    public function paidTickets()
    {
        
        return $this->belongsToMany(Ticket::class, 'bob_participant_ticket', 'participant_id', 'ticket_id')->wherePivot("sold", 1)->withPivot("purchase_id");

    }

    public function unpaidTickets()
    {
        
        return $this->belongsToMany(Ticket::class, 'bob_participant_ticket', 'participant_id', 'ticket_id')->wherePivot("sold", 0);

    }


 

        public function ssfields()
    {
     return $this->hasMany(ParticipantFields::class, "participant_id");
    }



    public function purchases()
    {
    	return $this->hasMany(Purchase::class, "participant_id");
    }





    public function setTokenAttribute($value)
    {
        $this->attributes['token'] = hash("sha1", $value . microtime(true) . mt_rand(1, mt_getrandmax()));
    }




}
