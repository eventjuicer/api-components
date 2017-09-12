<?php 

namespace Models;
use Illuminate\Database\Eloquent\Model;

class PostTags extends Model
{

	//http://stackoverflow.com/questions/23896031/how-to-save-entries-in-many-to-many-polymorphic-relationship-in-laravel/23896998#23896998

	 
	protected $table = 'editorapp_post_tag';
	
	
	protected $guarded = array('id');
	
	
	public $timestamps = false;
	

	function latestPosts()
	{
		 return $this->hasMany('Models\Post', "id", "xref_id")->orderBy("published_at", "DESC");
	}


	function tag()
	{
		 return $this->hasMany('Models\Tag', "id", "tag_id");
	}

}
