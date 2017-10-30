<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;


class CreativeTemplate extends Model
{
   

    protected $table = "eventjuicer_promo_templates";
    
    protected $guarded = [];


    protected $casts = [
        'data' => 'array',
    ];

    public function creatives()
    {
        return $this->hasMany(Creative::class);
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
      return $this->belongsTo(Group::class);

    }

}