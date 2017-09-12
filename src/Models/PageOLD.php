<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

use Services\Presenter\PresentableInterface;

use Presenters\GroupPage;

class PageOLD extends Model implements PresentableInterface
{
      protected $table = "bob_widgets";

      protected $dates = ["editedon"];
      

    public function getPresenter()
    {
        return new GroupPage($this);
    }



}
