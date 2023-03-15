<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPerformance extends Model {
     
    protected $table = "eventjuicer_company_performance";
    
    // protected $touches = ['company'];
    
    protected $casts = [

        'prizes' => 'array',
    ];

    protected $fillable = ["event_id", "company_id"];


    public function event()
    {   
        return $this->belongsTo(Event::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

  


}