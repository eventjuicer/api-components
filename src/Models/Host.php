<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;
use Eventjuicer\Models\Group;
use Eventjuicer\Models\Organizer;

class Host extends Model
{


    protected $table = "eventjuicer_hosts";

    protected $primaryKey = 'host';


    //  return $this->belongsTo('App\User', 'foreign_key', 'other_key');


    public function group()
    {
    	//foreign key
    	return $this->hasOne(Group::class, "id", "group_id");
    }


    public function organizer()
    {

    	return $this->hasOne(Organizer::class, "id", "organizer_id");
    }

   

    public function getPublishedonAttribute($value)
    {
        return date("d.m.Y H:i", $value);
    }



}
