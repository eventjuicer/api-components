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

  //  protected $primaryKey = 'name';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'data' ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['organizer_id', 'group_id', 'event_id', 'user_id']; //

    //public $timestamps = false;

    //protected $dates = ['updatedon'];


    public function settingable()
    {
        return $this->morphTo();
    }

}
