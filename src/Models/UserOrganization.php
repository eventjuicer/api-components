<?php namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class UserOrganization extends Model{

    public $incrementing = true;

    protected $table = "eventjuicer_user_organizations";

    public function organizer(){
        return $this->belongsTo(Organizer::class);
    }

    public function user(){
        return $this->belongsTo(CompanyAdmin::class);
    }


}