<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    	
	protected $table = "eventjuicer_user_settings";


    protected $fillable = ['name', 'hash','data'];


   	public function user()
    {
        return $this->belongsTo("Models\User");
    }




}
