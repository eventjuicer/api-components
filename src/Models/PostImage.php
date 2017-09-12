<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;



use Config;



class PostImage extends Model
{

    protected $table = "eventjuicer_images";

	protected $hidden = [
        "imageable_type",
        "imageable_id",
        "path_original",
        "path_original_hashed", 
        "organizer_id",
        "group_id",
        "event_id",
        "user_id"
    ];

    protected $fillable = ["path"];



    public function imageable()
    {
        return $this->morphTo();
    }




    



}
