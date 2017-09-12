<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class NewsdeskItem extends Model
{
    protected $table = "bob_newsdesk_items";



    public function organizer()
    {
        return $this->belongsTo("Models\Organizer", "id", "organizer_id");
    }


    public function group()
    {
        return $this->belongsTo("Models\Group", "id", "portal_id");
    }


    public function source()
    {
        return $this->belongsTo("Models\NewsdeskSource", "id", "source_id");
    }


}
