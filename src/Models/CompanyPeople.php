<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;
 

class CompanyPeople extends Model {


    protected $protected = [];

    protected $table = "eventjuicer_company_people";
    
    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function logs(){
        return $this->morphMany(UserLog::class, 'loggable');
    }


}
