<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Field;

// use Carbon\Carbon;
// use Cache;

use Eventjuicer\Services\Repository;
//use Bosnadev\Repositories\Eloquent\Repository;
use Illuminate\Support\Collection;

use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;
use Eventjuicer\Repositories\Criteria\BelongsToOrganizer;
use Eventjuicer\Repositories\Criteria\WhereIn;

class InputRepository extends Repository
{
    

    public function model()
    {
        return Field::class;
    }

    public function filtered(array $names) : Collection
    {
        return $this->all()->whereIn("name", $names)->pluck("name", "id");
    }

    public function toArray() : array
    {
        return $this->all()->pluck("name", "id")->toArray();
    }




    public function getParticipantsWithFields(array $names, string $scope, int $scopeId, $cache = 1) : \Illuminate\Support\Collection
    {


        $this->with(["participants", "participants.fields"]);

        $this->pushCriteria(new WhereIn("name", $names));


        // switch($scope)
        // {
        //     case "event":
        //         $this->pushCriteria(new BelongsToEvent($eventId));
        //     break;

        //     case "group":
        //         $this->pushCriteria(new BelongsToGroup($eventId));
        //     break;

        //     case "organizer":
        //         $this->pushCriteria(new BelongsToOrganizer($eventId));
        //     break;
        // }


        return $this->all()->pluck("participantsNotCancelled")->collapse();



        return $this->cached($role . $eventId, (int) $cache, function() use ($role, $eventId)
        {

           

        });

    }

}