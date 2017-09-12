<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class Portal extends Model
{
    protected $table = "bob_portals";

    public $timestamps = false;

    public function organizer()
    {
    	return $this->belongsTo("Models\Organizer");
    }
}
