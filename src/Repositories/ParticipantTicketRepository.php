<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\ParticipantTicket;
use Eventjuicer\Repositories\Repository;
 

class ParticipantTicketRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return ParticipantTicket::class;
    }

 



}