<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;



class CompanyData extends Model
{
     
    protected $table = "eventjuicer_company_data";
       
    
    protected $casts = [

        'data' => 'array',
    ];


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




}
