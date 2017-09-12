<?php

namespace Eventjuicer\Models;
use Illuminate\Database\Eloquent\Model;

class Parsable extends Model
{
    
    protected $table = "eventjuicer_parsables";

    protected $casts = ["data" => "array"];
      


 	  public function organizer()
      {
      		return $this->belongsTo("Models\Organizer");
      }

  	  public function group()
      {
      		return $this->belongsTo("Models\Group");
      }

      public function source()
      {
      		return $this->belongsTo("Models\ParsableSource", "id", "parsable_source_id");
      }

      public function parsable()
      {
        	return $this->morphTo();
      }



}
