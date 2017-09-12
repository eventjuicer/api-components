<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class SenderDelivery extends Model
{
    protected $table = "eventjuicer_sender_deliveries";

    public $timestamps = false;

    public function deliverable()
    {
    	 return $this->morphTo();
    }

}
