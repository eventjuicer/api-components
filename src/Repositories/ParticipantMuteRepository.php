<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\ParticipantMute;
use Eventjuicer\Repositories\Repository;

use Carbon\Carbon;
 


class ParticipantMuteRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;


    public function model()
    {
        return ParticipantMute::class;
    }

    public function updateAfterSend(string $email, int $eventId)
    {
    	$model = $this->makeModel();

    	$model->email = $email;
    	$model->event_id = $event_id;

    	$model->save();

        return $model->id;
    }
    

}