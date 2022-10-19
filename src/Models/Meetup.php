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

    
    protected $dates = [

        'sent_at',
        'resent_at',
        'responded_at',
        'scheduled_at'
    
    ];
   
    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function participant()
    {   
        return $this->belongsTo(Participant::class);
    }

    public function presenter()
    {   
        return $this->belongsTo(Participant::class, "rel_participant_id");
    }

    public function admin()
    {
        return $this->belongsTo(Participant::class, "user_id", "id");
    }






}
