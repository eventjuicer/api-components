<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Presenters\GroupPage;

class PageOLD extends Model
{
      protected $table = "bob_widgets";

      protected $dates = ["editedon"];
      

    public function getPresenter()
    {
        return new GroupPage($this);
    }



}
