<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class NewsdeskSource extends Model
{
    protected $table = "bob_newsdesk_sources";



    public function organizer()
    {
        return $this->belongsTo("Models\Organizer", "id", "organizer_id");
    }


    public function group()
    {
        return $this->belongsTo("Models\Group", "id", "portal_id");
    }

    public function items()
    {
        return $this->hasMany("Models\NewsdeskItem", "source_id");
    }

    


}
