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
   		return $this->hasMany(Group::class, "organizer_id");
   	}

   	public function events()
   	{
   		return $this->hasMany(Event::class, "organizer_id");
   	}

   	public function portals()
   	{
   		return $this->hasMany(Portal::class, "organizer_id");
   	}

      public function hosts()
      {
          return $this->hasMany(Host::class, "organizer_id");
      }

      public function users()
      {
         return $this->belongsToMany(User::class, "eventjuicer_user_organizations", "organizer_id", "user_id");
      }


      public function imports()
      {
         return $this->hasMany(SenderImport::class, "organizer_id");

      }

      public function newsletters()
      {
         return $this->hasMany(SenderNewsletter::class, "organizer_id");

      }

      public function campaigns()
      {
         return $this->hasMany(SenderCampaign::class, "organizer_id")->orderBy("scheduled_at", "DESC");

      }

}
