<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

use Services\AbleTrait;
use Models\Scan;


class Participant extends Model
{

    use AbleTrait;
    

    protected $table = "bob_participants";

 	protected $hidden = ['token'];

    protected $guarded = ["event_id", "group_id", "organizer_id"];


    public $timestamps = false;

    

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






    public function scans()
    {
        return $this->hasMany(Scan::class, "owner_id")->orderby("id", "DESC");
    }

    public function scannedParticipants()
    {
        
        return $this->belongsToMany('Models\Participant', 'eventjuicer_barcode_scans', 'owner_id', 'scanned_id')->withPivot("scanned_at");

    }



    public function ScanOwners()
    {
        return $this->hasMany(Scan::class, "owner_id");
    }

    
    public function organizer()
    {
        return $this->belongsTo("Models\Organizer");
    }

    public function group()
    {
        return $this->belongsTo("Models\Group");
    }

    public function event()
    {
        return $this->belongsTo("Models\Event");
    }

     public function paidTickets()
    {
        
        return $this->belongsToMany('Models\Ticket', 'bob_participant_ticket', 'participant_id', 'ticket_id')->wherePivot("sold", 1);

    }

    public function fields()
    {
        
        return $this->belongsToMany('Models\Field', 'bob_participant_fields', 'participant_id', 'field_id')->withPivot("field_value","participant_id");//->withTimestamps();//->wherePivot("sold", 1);

    }

        public function ssfields()
    {
     return $this->hasMany("Models\ParticipantFields", "participant_id");
    }



    public function purchases()
    {
    	return $this->hasMany("Models\Purchase", "participant_id");
    }





    public function setTokenAttribute($value)
    {
        $this->attributes['token'] = hash("sha1", $value . microtime(true) . mt_rand(1, mt_getrandmax()));
    }




}
