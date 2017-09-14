<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Models\Traits\AbleTrait;

class Organizer extends Model
{

      use AbleTrait;
	

	   public $timestamps = false;
     
   	protected $table = "bob_organizers";


   	public function groups()
   	{
   		return $this->hasMany("Models\Group", "organizer_id");
   	}

   	public function events()
   	{
   		return $this->hasMany("Models\Event", "organizer_id");
   	}

   	public function portals()
   	{
   		return $this->hasMany("Models\Portal", "organizer_id");
   	}

      public function hosts()
      {
          return $this->hasMany("Models\Host", "organizer_id");
      }

      public function users()
      {
         return $this->belongsToMany('Models\User', "eventjuicer_user_organizations", "organizer_id", "user_id");
      }


      public function imports()
      {
         return $this->hasMany("Models\SenderImport", "organizer_id");

      }

      public function newsletters()
      {
         return $this->hasMany("Models\SenderNewsletter", "organizer_id");

      }

      public function campaigns()
      {
         return $this->hasMany("Models\SenderCampaign", "organizer_id")->orderBy("scheduled_at", "DESC");

      }

}
