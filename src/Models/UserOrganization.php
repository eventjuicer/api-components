<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class UserOrganization extends Model
{


    protected $table = "eventjuicer_user_organizations";


    public function organizer()
    {
        return $this->belongsTo("Models\Organizer");
    }

    public function user()
    {
        return $this->belongsTo("Models\User");
    }


}