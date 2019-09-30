<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
   
     /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'eventjuicer_settings';

    protected $fillable = ['name', 'data'];

    // protected $dates = ['updatedon'];

    // public $timestamps = false;
    
    public function settingable()
    {
        return $this->morphTo();
    }

}
