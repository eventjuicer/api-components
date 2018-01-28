<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Models\Company;
use Eventjuicer\Models\Participant;
use Eventjuicer\Models\Organizer;
use Eventjuicer\Models\Group;

class CompanyImport extends Model
{
     
    protected $table = "eventjuicer_company_imports";
       
    
    protected $casts = [

        'data' => 'array',
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
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
