<?php

 
namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;



class TicketGroup extends Model
{



    protected $table = "bob_ticket_groups";

    protected $casts = [

        'descriptions' => 'array',
        'booth' => 'array'
    ];
    
    public function tickets()
    {
        
        return $this->hasMany(Ticket::class, "ticket_group_id");

    }

  

}
