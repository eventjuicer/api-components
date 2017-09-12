<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = "eventjuicer_comments";

    protected $casts = array(

    	"jsondata" => "array"
    );

    protected $fillable = ["comment", "jsondata", "resolved_at", "is_task", "recipient_user_id"];


    protected $hidden = array("commentable_id", "commentable_type", "organizer_id", "group_id", "event_id", "updated_at", "author", "recipient");


    protected $appends = ["author_name", "recipient_name"];



    public function commentable()
    {
        return $this->morphTo();
    }

    public function getAuthorNameAttribute()
    {
    	return $this->author->fname . " " .$this->author->lname;
    }

    public function getRecipientNameAttribute()
    {
    	return $this->recipient_user_id ? $this->recipient->fname . " " . $this->recipient->lname : "";
    }

    public function author()
    {
    	return $this->hasOne("Models\User", "id", "user_id");
    }

 	public function recipient()
    {
    	return $this->hasOne("Models\User", "id", "recipient_user_id");
    }











}