<?php

namespace Eventjuicer\Listeners;

use Eventjuicer\Jobs\SendSlackNotificationJob;
use Eventjuicer\Events\NewItemPurchased;
use Eventjuicer\Services\ParticipantRoles;

class SendSlackNotificationListener {


    public function __construct(){}

    public function handle(NewItemPurchased $event){    

        $participant = $event->data;

        //check ticket role!

        $isVisitor = (new ParticipantRoles($event->data))->hasRole("visitor");

        if($isVisitor){
            return;
        }

        if($participant->organizer_id > 1){
            return;
        }

        dispatch( new SendSlackNotificationJob( 

            json_encode($participant->email)

        ) );
    }


}




