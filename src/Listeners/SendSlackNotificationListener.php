<?php

namespace Eventjuicer\Listeners;

use Eventjuicer\Jobs\SendSlackNotificationJob;
use Eventjuicer\Events\NewItemPurchased;
use Eventjuicer\Crud\Participants\ParticipantRoles;

class SendSlackNotificationListener {


    public function __construct(){}

    public function handle(NewItemPurchased $event){    

        $participant = $event->data;
        $roles = new ParticipantRoles($event->data);

        //check ticket role!

        if( $roles->hasRole("visitor") ){
            return;
        }

        // if($participant->organizer_id > 1){
        //     return;
        // }

        dispatch( new SendSlackNotificationJob( 

            $participant->email . " " . strval($roles),
            $participant->organizer_id

        ) );
    }


}




