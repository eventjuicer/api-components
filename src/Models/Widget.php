<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

use Services\Presenter\PresentableInterface;

use Presenters\GroupWidget;

class Widget extends Model
{
      protected $table = "bob_widgets";

      protected $dates = ["editedon"];
      

    public function getPresenter()
    {
        return new GroupWidget($this);
    }


    public function widgetable()
    {
        return $this->morphTo();
    }

}
