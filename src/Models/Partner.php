<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;
 

class Partner extends Model
{


    protected $fillable = ['name','description','link','avatar'];

    protected $table = "bob_parties";
    
  //  protected $dates = ['assigned_at', 'stats_updated_at'];

    public $timestamps = false;

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }





}
