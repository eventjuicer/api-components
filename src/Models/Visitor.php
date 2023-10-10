<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;
 
class Visitor extends Model
{

    // protected $fillable = [];

    protected $table = "eventjuicer_visitors";
    
    // protected $dates = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
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


}
