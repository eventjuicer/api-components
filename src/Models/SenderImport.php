<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class SenderImport extends Model
{
    protected $table = "eventjuicer_sender_imports";


    public function emails()
    {
    	return $this->hasMany("Models\SenderEmail", "import_id");
    }


    public function campaigns()
    {
    	//return $this->belongsToMany('App\Role', 'user_roles', 'user_id', 'role_id');

    	return $this->belongsToMany('Models\SenderCampaign', "eventjuicer_sender_campaign_include", "import_id", "campaign_id")->withTimestamps()->withPivot("started", "prepared");

    }


}
