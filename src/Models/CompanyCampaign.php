<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Models\Company;
use Eventjuicer\Models\Participant;
use Eventjuicer\Models\Organizer;
use Eventjuicer\Models\Group;


use Eventjuicer\Models\CompanyContactlist;
use Eventjuicer\Models\Creative;

class CompanyCampaign extends Model
{
     
    protected $table = "eventjuicer_company_campaigns";
       
    
    protected $casts = [

        'data' => 'array',
    ];

    protected $dates = ['scheduled_at'];



    public function organizer()
    {   
        return $this->belongsTo(Organizer::class);
    }

    
    public function group()
    {   
        return $this->belongsTo(Group::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function admin()
    {
        return $this->belongsTo(Participant::class, "user_id", "id");
    }


    public function contactlists()
    {
        return $this->belongsToMany(CompanyContactlist::class, "eventjuicer_company_campaign_contactlist", "campaign_id", "contactlist_id")->withTimestamps();
    }

    public function creatives()
    {
        return $this->belongsToMany(Creative::class, "eventjuicer_company_campaign_creative", "campaign_id", "creative_id")->withTimestamps();
    }








}
