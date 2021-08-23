<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model {

    protected $table = "eventjuicer_userlog";

    protected $casts = [
        'data' => 'array',
        'backup' => 'array',
    ];

    protected $fillable = ["*"];



    public function loggable()
    {
        return $this->morphTo();
    }




    



}
