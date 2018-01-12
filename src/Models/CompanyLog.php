<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;


class CompanyLog extends Model
{


    protected $table = "eventjuicer_company_log";

    protected $guarded = ["event_id", "group_id", "organizer_id"];

    protected $casts = [

        'data' => 'array',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class, "id", "user_id");
    }

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

    public function loggable()
    {
        return $this->morphTo();
    }




}