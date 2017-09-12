<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class CostTag extends Model
{
     
	protected $table = 'costapp_document_tags';
	
	
	protected $guarded = array('id');
	
	
	public $timestamps = false;
	

	function latestCosts()
	{
		 return $this->hasMany('Models\Cost', "id", "xref_id")->orderBy("originated_at", "DESC");
	}


	function tag()
	{
		 return $this->hasMany('Models\Tag', "id", "tag_id");
	}


}
