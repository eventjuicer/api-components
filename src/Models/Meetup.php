<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Models\Company;
use Eventjuicer\Models\Participant;


class Meetup extends Model
{

     

    protected $table = "eventjuicer_meetups";
    

    protected $casts = [

        'data' => 'array',
    ];

       
    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function participant()
    {   
        return $this->belongsTo(Participant::class);
    }


    public function admin()
    {
        return $this->belongsTo(Participant::class, "user_id", "id");
    }






}
