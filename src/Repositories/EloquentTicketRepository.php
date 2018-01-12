<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Ticket;
// use Carbon\Carbon;
// use Cache;


use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;
use Eventjuicer\Repositories\Criteria\BelongsToOrganizer;

use Illuminate\Support\Collection;


class EloquentTicketRepository extends Repository implements TicketRepositoryInterface
{
    

    public function model()
    {
        return Ticket::class;
    }



    public function getTicketsWithRole(string $role, int $eventId)
    {
          $this->pushCriteria(new ColumnMatches("role", $role));

          $this->pushCriteria(new BelongsToEvent($eventId));

          return $this->all();
    }

    
    


    public function getParticipantsWithTicketRole(string $role, string $scope, int $eventId, $cache= 1) : Collection
    {

       return $this->cached($role . $scope . $eventId, (int) $cache, function() use ($role, $scope, $eventId)
        {

            $this->with([
                "participantsNotCancelled", 
             //  "participantsNotCancelled.fields", 
                //"participantsNotCancelled.tickets"
        ]);

            $this->pushCriteria(new ColumnMatches("role", $role));

            switch($scope)
            {
                case "event":
                    $this->pushCriteria(new BelongsToEvent($eventId));
                break;

                case "group":
                    $this->pushCriteria(new BelongsToGroup($eventId));
                break;

                case "organizer":
                    $this->pushCriteria(new BelongsToOrganizer($eventId));
                break;
            }

            return $this->all()->pluck("participantsNotCancelled")->collapse();

       });

    }



}