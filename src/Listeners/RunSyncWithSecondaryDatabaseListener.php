<?php

namespace Eventjuicer\Listeners;

use Eventjuicer\Events\NewItemPurchased;
use Eventjuicer\Jobs\RunSyncWithSecondaryDatabaseJob;
// use Eventjuicer\Crud\Participants\ParticipantRoles;


class RunSyncWithSecondaryDatabaseListener {


    public function __construct(){}

    public function handle(NewItemPurchased $event){    

        $participant = $event->data; //participant Object!
        $config = $event->config; //postData!
        // $template = array_get($event->config, "template", false);
        // $profile = new Personalizer($participant);
        // $roles = new ParticipantRoles($event->data);
        // if( $roles->hasRole("visitor") ){
        //     return;
        // }

        // if(intval($participant->organizer_id) === 1){
        //     return;
        // }

         dispatch( new RunSyncWithSecondaryDatabaseJob( $participant->id ) );
    }

}




