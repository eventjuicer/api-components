<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Models\Company;
use Eventjuicer\Models\Participant;
use Eventjuicer\Models\Organizer;
use Eventjuicer\Models\Group;
use Eventjuicer\Models\CompanyContact;

class CompanyContactlist extends Model
{
     
    protected $table = "eventjuicer_company_contactlists";
       


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function contacts()
    {
        return $this->belongsToMany(CompanyContact::class, "eventjuicer_company_contact_contactlist", "contactlist_id", "contact_id")->withTimestamps();
    }


    public function organizer()
    {   
        return $this->belongsTo(Organizer::class);
    }

    public function group()
    {   
        return $this->belongsTo(Group::class);
    }

    public function admin()
    {
        return $this->belongsTo(Participant::class, "user_id", "id");
    }






}
