<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;


class Widget extends Model
{
    protected $table = "bob_widgets";

    protected $dates = ["editedon"];
     
     protected $casts = [

        'data' => 'array',
    ];


    public function widgetable()
    {
        return $this->morphTo();
    }

}
