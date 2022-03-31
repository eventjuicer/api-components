<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyVipcode extends Model {
     
    protected $table = "eventjuicer_company_vipcodes";
    
    protected $touches = ['company'];


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

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }


}
