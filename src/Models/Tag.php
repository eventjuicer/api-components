<?php

namespace Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
	
	
	protected $table = 'bob_tags';
	
	public $timestamps = false;

	protected $guarded = array('id');
	
	

	function users()
	{
		return $this->belongsToMany('Models\User');
	}

	function posts()
	{
    	return $this->belongsToMany('Models\Post', "editorapp_post_tag", "tag_id", "xref_id")->withPivot('organizer_id');
	}


	function costTags()
	{
		return $this->hasMany('Models\CostTag');
	}

	function costs()
	{
    	return $this->belongsToMany('Models\Cost', "costapp_document_tags", "tag_id", "xref_id")->withPivot('organizer_id', 'group_id', 'event_id', 'originated_at', 'created_at');
	}

	function latestPosts()
	{
    	return $this->belongsToMany('Models\Post', "editorapp_post_tag", "tag_id", "xref_id")->withPivot('organizer_id')->orderBy("publishedon", "DESC");
	}


	function posttags()
	{
    	return $this->hasMany('Models\PostTags');
	}


    public function categories()
    {
        return $this->morphedByMany('Models\Category', 'taggable');
    }



 

}
