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
    protected $table = 'bob_settings';

    protected $fillable = ['name', 'data' ];

    protected $dates = ['updatedon'];

    public $timestamps = false;

    //protected $dates = ['updatedon'];


    // public function settingable()
    // {
    //     return $this->morphTo();
    // }

}
