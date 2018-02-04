<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Models\Company;
use Eventjuicer\Models\Participant;
use Eventjuicer\Models\Organizer;
use Eventjuicer\Models\Group;
use Eventjuicer\Models\CompanyContact;
use Eventjuicer\Models\CompanyContactlist;


class CompanyImport extends Model
{
     
    protected $table = "eventjuicer_company_imports";
       
    
    protected $casts = [

        'data' => 'array',
    ];


    protected $dates = [
        'imported_at'
    ];



    public function contacts()
    {
         return $this->hasMany(CompanyContact::class);
    }


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function contactlist()
    {
        return $this->belongsTo(CompanyContactlist::class);
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
