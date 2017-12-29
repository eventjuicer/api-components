<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Models\Participant;

use Eventjuicer\Models\Organizer;


class Company extends Model
{

     

    protected $table = "eventjuicer_companies";
    
   

    
    public function company()
    {
        return $this->belongsTo(Participant::class, "id", "owner_id");
    }

    public function assignedBy()
    {
            return $this->belongsTo(Participant::class, "id", "owner_id");

    }

    public function participants()
    {   
        return $this->hasMany(Participant::class);
    }


    public function organizer()
    {
        return $this->belongsTo(Organizer::class, "id", "scanned_id");
    }






}
