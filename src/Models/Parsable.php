<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

use Services\Presenter\PresentableInterface;

use Presenters\GroupWidget;

class Parsable extends Model implements PresentableInterface
{
    
    protected $table = "eventjuicer_parsables";

    protected $casts = ["data" => "array"];
      

   // public function getPresenter()
    //{
      //  return new GroupWidget($this);
    //}


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
