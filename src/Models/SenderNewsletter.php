<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Services\AbleTrait;


class SenderNewsletter extends Model
{

	use AbleTrait;



    protected $table = "eventjuicer_sender_newsletters";

    protected $guarded = ["organizer_id", "user_id"]; 


    function getLabelAttribute()
    {
    	return $this->name . " " . $this->updated_at;
    }
 

 	public function parse($key)
	{
		return \Parse::parse( $this->{$key} );
	}

    
}
