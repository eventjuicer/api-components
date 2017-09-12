<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

use Services\Presenter\PresentableInterface;

use Presenters\GroupWidget;

class ParsableSource extends Model implements PresentableInterface
{
      protected $table = "eventjuicer_parsable_sources";

    //  protected $dates = ["editedon"];
      

   // public function getPresenter()
    //{
      //  return new GroupWidget($this);
    //}


 	  public function parsableParent()
      {
      	
      }

  	  public function parsableChild()
      {
      	
      }

      public function organizer()
      {

      }



}
