<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class PostStat extends Model
{
    

  
 	protected $primaryKey = 'post_id';

    public $incrementing = false;

    public $timestamps = false;

    protected $table = "editorapp_post_stats";


    public function post()
    {
    	return $this->belongsTo("Models\Post", "id", "post_id");
    }



}
