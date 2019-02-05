<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class SenderCampaign extends Model
{
    protected $table = "eventjuicer_sender_campaigns";

    protected $guarded = ["organizer_id", "import_ids"];

    protected $casts = [

    ];


    public function sender()
    {
    	return $this->from_name . " <" . $this->from_address. ">";
    }


    public function includes()
    {
    	//return $this->belongsToMany('App\Role', 'user_roles', 'user_id', 'role_id');

    	return $this->belongsToMany(SenderImport::class, 
            "eventjuicer_sender_campaign_include" , "campaign_id", "import_id")->withTimestamps();

    }


    public function excludes()
    {
        //return $this->belongsToMany('App\Role', 'user_roles', 'user_id', 'role_id');

        return $this->belongsToMany(SenderImport::class, 
            "eventjuicer_sender_campaign_exclude" , "campaign_id", "import_id")->withTimestamps();

    }


    public function undettachableIncludes()
    {
        //return $this->belongsToMany('App\Role', 'user_roles', 'user_id', 'role_id');

        return $this->belongsToMany(SenderImport::class, "eventjuicer_sender_campaign_include" , "campaign_id", "import_id")->where("started", 1)->withTimestamps();

    }

    public function undettachableExcludes()
    {
        //return $this->belongsToMany('App\Role', 'user_roles', 'user_id', 'role_id');

        return $this->belongsToMany(SenderImport::class, "eventjuicer_sender_campaign_exclude" , "campaign_id", "import_id")->where("started", 1)->withTimestamps();

    }



    public function isEditable()
    {
        
    }


    public function participants()
    {
    	//it will be much more complicated as we will have datasets here
    }


}
