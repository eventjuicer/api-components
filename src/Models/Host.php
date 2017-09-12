<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Host extends Model
{


    protected $table = "eventjuicer_hosts";

    protected $primaryKey = 'host';


    //  return $this->belongsTo('App\User', 'foreign_key', 'other_key');


    public function group()
    {
    	//foreign key
    	return $this->hasOne("Models\Group", "id", "group_id");
    }


    public function organizer()
    {

    	return $this->hasOne("Models\Organizer", "id", "organizer_id");
    }

   

    public function getPublishedonAttribute($value)
    {
        return date("d.m.Y H:i", $value);
    }



}
