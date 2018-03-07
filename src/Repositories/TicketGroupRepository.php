<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\TicketGroup;

 

class TicketGroupRepository extends Repository
{
    

    protected $preventCriteriaOverwriting = false;


    public function model()
    {
        return TicketGroup::class;
    }


 

}