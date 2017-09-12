<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

use Services\AbleTrait;


class Group extends Model
{

    use AbleTrait;

    protected $table = "bob_event_groups";
    
    public $timestamps = false;

    
    public function events()
    {
        return $this->hasMany("Models\Event", "group_id")->orderBy("id", "DESC");;
    }


    public function latestEvents()
    {
        return $this->hasMany("Models\Event", "group_id")->orderby("id", "DESC");
    }



    public function organizer()
    {
    	return $this->belongsTo("Models\Organizer");
    }

    public function is_portal()
    {

    }

    public function topics()
    {
        return $this->hasMany("Models\Topic", "group_id");
    }






}
