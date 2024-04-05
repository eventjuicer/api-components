<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\ParticipantDelivery;
use Eventjuicer\Repositories\Repository;

use Carbon\Carbon;
 
use Eventjuicer\Models\Event;

class ParticipantDeliveryRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;


    public function model()
    {
        return ParticipantDelivery::class;
    }

    public function updateAfterSend(string $email, int $eventId, string $context = "p")
    {

        $event = Event::find($eventId);

    	$model = $this->makeModel();

    	$model->email = trim(strtolower($email));

    	$model->event_id = $eventId;
        $model->group_id = $event->group_id;
        $model->organizer_id = $event->organizer_id;
        $model->context = $context;
        
    	$model->save();

        return $model->id;
    }
    

}