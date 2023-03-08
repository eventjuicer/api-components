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

        if( $roles->hasRole("presenter") ){
            return;
        }

        if( $roles->hasRole("juror") ){
            return;
        }


        if( $roles->hasRole("party") ){
            return;
        }

        if( $roles->hasRole("contestant*") ){
            return;
        }

        if( $roles->hasRole("representative") ){
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




