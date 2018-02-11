<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\ParticipanDelivery;
use Eventjuicer\Repositories\Repository;

use Carbon\Carbon;
 
use Eventjuicer\Models\Event;

class ParticipantDeliveryRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;


    public function model()
    {
        return ParticipanDelivery::class;
    }

    public function updateAfterSend(string $email, int $eventId)
    {

        $event = Event::find($eventId);

    	$model = $this->makeModel();

    	$model->email = $email;
    	$model->event_id = $eventId;
        $model->group_id = $event->group_id;
        $model->organizer_id = $event->organizer_id;

    	$model->save();

        return $model->id;
    }
    

}