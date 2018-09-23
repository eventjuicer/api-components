<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;


use Eventjuicer\Models\Traits\AbleTrait;


class Group extends Model
{

    use AbleTrait;

    protected $table = "bob_event_groups";
    
    public $timestamps = false;

    protected $fillable = ['active_event_id'];

    
    public function events()
    {
        return $this->hasMany(Event::class, "group_id");
    }

    public function activeEvent()
    {

        return $this->events()->where(function($el){

           return $el->id = $this->active_event_id;
        });

    }


    public function latestEvents()
    {
        return $this->hasMany(Event::class, "group_id")->orderby("id", "DESC");
    }



    public function organizer()
    {
    	return $this->belongsTo(Organizer::class);
    }

    public function is_portal()
    {

    }

    public function topics()
    {
        return $this->hasMany(Topic::class, "group_id");
    }






}
