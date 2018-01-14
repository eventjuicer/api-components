<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;
 

class Company extends Model
{

     

    protected $table = "eventjuicer_companies";
    
   

    public function assignedBy()
    {
            return $this->belongsTo(Participant::class, "id", "owner_id");

    }

    public function participants()
    {   
        return $this->hasMany(Participant::class);
    }

    public function creatives()
    {
        return $this->hasMany(Creative::class);
    }


    public function organizer()
    {
        return $this->belongsTo(Organizer::class, "id", "scanned_id");
    }

    
    public function group()
    {
        return $this->belongsTo(Group::class, "id", "owner_id");
    }







}
