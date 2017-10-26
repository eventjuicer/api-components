<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Ticket;
use Illuminate\Support\Collection;

interface TicketRepositoryInterface
{
    
    public function getParticipantsWithTicketRole(string $role, string $scope, int $eventId, $cache= 1) : Collection;

}