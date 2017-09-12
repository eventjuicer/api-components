<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Organizer;

class UserInvitation extends Model
{
    protected $table = "eventjuicer_user_invitations";

    protected $primaryKey = "code";


    //  return $this->belongsTo('App\User', 'foreign_key', 'other_key');



    public function user()
    {
    	return $this->belongsTo("Models\User", "user_id");
    }

    public function organizer()
    {
        return $this->belongsTo("Models\Organizer", "organizer_id");
    }

  //  public function organizer()
    //{
      //  return \Eventjuicer\Organizer::find($this->user->organizer_id);
    //}

  //  public function getCreatedAtAttribute($value)
    //{
    //	return strtotime($value);
    //}

    public function codeIsActive()
    {
    	return (bool) $this->active;
    }

}
