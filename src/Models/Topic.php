<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;


use Eventjuicer\Services\AbleTrait;


class Topic extends Model
{

    use AbleTrait;
    
	protected $table = "eventjuicer_portal_topics";


	protected $hidden = ["organizer_id", "image_id"];

    protected $fillable = ["title", "description", "tags"];

    public $timestamps = false;

	protected $casts = array(

		//"tags" => "array"
	);




  //  public function getTagsAttribute($value)
    //{

      //  return json_decode($value, true);
    //}

    public function image()
    {
        return $this->hasOne("Models\PostImage", "id", "image_id");
    }

	

    public function organizer()
    {
    	return $this->hasOne("Models\Organizer");
    }

     public function portal()
    {
    	return $this->hasOne("Models\Group");
    }


  	public function group()
    {
    	return $this->hasOne("Models\Group", "id", "group_id");
    }

    public function posts()
    {

    	//get tags!

    }



    function latestPosts()
    {
        return $this->belongsToMany('Models\Post', "eventjuicer_post_topic", "topic_id", "xref_id")->withPivot('organizer_id','group_id')->orderBy("publishedon", "DESC");
    }





}