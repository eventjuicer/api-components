<?php

 
namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;



class TicketGroup extends Model
{



    protected $table = "bob_ticket_groups";
    public $timestamps = false;

    protected $casts = [

        'descriptions' => 'array',
        'booth' => 'array',
        "json" => "array"
    ];
    
    public function tickets()
    {
        
        return $this->hasMany(Ticket::class, "ticket_group_id");

    }

  	
    public function oldtags()
    {
        
        return $this->belongsToMany(Tag::class, 'bob_taggings', 'object_id', 'tag_id')->wherePivot("object_name", "ticket_group")->withPivot("createdon");

    }



}
