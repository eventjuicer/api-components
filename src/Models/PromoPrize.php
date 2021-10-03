<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;


class PromoPrize extends Model {
   
    protected $table = "eventjuicer_promo_prizes";
    
    protected $guarded = [];

    // protected $casts = [];

    public function organizer(){
      return $this->belongsTo(Organizer::class);
    }

    public function group(){
    	return $this->belongsTo(Group::class);

    }

}