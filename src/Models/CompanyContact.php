<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Models\Company;
use Eventjuicer\Models\Participant;
use Eventjuicer\Models\Organizer;
use Eventjuicer\Models\Group;
use Eventjuicer\Models\CompanyContactlist;
use Eventjuicer\Models\CompanyImport;

class CompanyContact extends Model
{
     
    protected $table = "eventjuicer_company_contacts";
       
    
    protected $casts = [

        'data' => 'array',
    ];

    protected $dates = [

        'sent_at'
    
    ];



    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function contactlists()
    {
        return $this->belongsToMany(CompanyContactlist::class, "eventjuicer_company_contact_contactlist", "contact_id", "contactlist_id")->withTimestamps();
    }


    public function organizer()
    {   
        return $this->belongsTo(Organizer::class);
    }

    public function import()
    {   
        return $this->belongsTo(CompanyImport::class);
    }


    public function group()
    {   
        return $this->belongsTo(Group::class);
    }




    // public function admin()
    // {
    //     return $this->belongsTo(Participant::class, "user_id", "id");
    // }






}
