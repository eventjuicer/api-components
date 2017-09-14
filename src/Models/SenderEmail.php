<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Models\Traits\AbleTrait;

class SenderEmail extends Model
{

	use AbleTrait;


    protected $table = "eventjuicer_sender_emails";

    protected $fillable = ["organizer_id", "email"];

    public function import()
    {
    	return $this->belongsTo("Models\SenderImport");
    	//return $this->belongsTo('App\Post', 'foreign_key', 'other_key');

    }



}
