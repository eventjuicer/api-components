<?php

namespace Eventjuicer\Models;

use App\User as BaseUser;

//use Zizaco\Entrust\Traits\EntrustUserTrait;

use Laravel\Passport\HasApiTokens;

class User extends BaseUser
{
   
   use HasApiTokens;
   use Traits\AbleTrait;
    
    //use EntrustUserTrait;
    //use \SammyK\LaravelFacebookSdk\SyncableGraphNodeTrait;


    protected $casts = [
        'profile' => 'array',
    ];



    protected static $graph_node_field_aliases = [
        'id' => 'facebook_user_id',
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'eventjuicer_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fname', 'lname','email','profile'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function apps()
    {
        return $this->hasMany(UserSetting::class);
    }

    public function settings()
    {
        return $this->hasMany(UserSetting::class);
    }

 
    public function organizations()
    {

        //return $this->belongsToMany('Models\Organizer', "eventjuicer_user_organizations", "user_id", "organizer_id");
        return $this->belongsToMany(Organizer::class, "eventjuicer_user_organizations");

    }


    public function posts()
    {
        return $this->hasMany("Models\Post", "admin_id");
    }



 //   public function getProfileAttribute($value)
   // {
     //   return json_decode($value, true);
    //}


    public function avatar()
    {
        if(!empty($this->profile["image_id"]) && ($model = \Eventjuicer\PostImage::find( $this->profile["image_id"] )))
        {
            return $model->path;
        }

        return  "http://www.placehold.it/400x400/EFEFEF/AAAAAA&amp;text=no+avatar";
    }


    public function setImageIdAttribute($value)
    {
        if(!$value)
        {
            return $this->profile["image_id"];
        }

        $profile = $this->profile;
        $profile["image_id"] = $value;
        $this->attributes["profile"] = json_encode($profile);
    }






}