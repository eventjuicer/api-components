<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;


class Creative extends Model
{
   

    protected $table = "eventjuicer_company_creatives";
    
    protected $guarded = [];


    protected $casts = [

        'data' => 'array',
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function template()
    {
        return $this->belongsTo(CreativeTemplate::class);
    }


    public function organizer()
    {
      return $this->belongsTo(Organizer::class);
    }


    public function group()
    {
    	return $this->belongsTo(Group::class);

    }

 

}