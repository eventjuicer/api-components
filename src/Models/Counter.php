<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    
	
	protected $table = "eventjuicer_counters";


    public function counterable()
    {
        return $this->morphTo();
    }




	//public function setHashAttribute($value)
	//{
	//$this->attributes['hash'] = md5(IP . $this->name); 
	//}


}
