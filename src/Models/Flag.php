<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;


class Flag extends Model
{

    
    protected $table = "eventjuicer_flags";

    protected $fillable = ["name", "data"];

   
    public function flaggable()
    {
        return $this->morphTo();
    }




}
