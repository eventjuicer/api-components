<?php

namespace Eventjuicer\Listeners;

use Eventjuicer\Jobs\SendOrderConfirmationEmailJob;
use Eventjuicer\Events\NewItemPurchased;
use Eventjuicer\Crud\Participants\ParticipantRoles;

class SendOrderConfirmationEmailListener {


    public function __construct(){}

    public function handle(NewItemPurchased $event){    

        $template = array_get($event->config, "template", false);

        /**
         * do nothing if template is absent
         */
        if(!$template){
            return;
        }

        // $roles = new ParticipantRoles($event->data);

        // //check ticket role!

        // if( $roles->hasRole("visitor") ){
        //     return;
        // }

        // if( $roles->hasRole("presenter") ){
        //     return;
        // }

        // if( $roles->hasRole("juror") ){
        //     return;
        // }


        // if( $roles->hasRole("party") ){
        //     return;
        // }

        // if( $roles->hasRole("contestant*") ){
        //     return;
        // }

        // if( $roles->hasRole("representative") ){
        //     return;
        // }

        // if($participant->organizer_id > 1){
        //     return;
        // }

        dispatch( new SendOrderConfirmationEmailJob(  $event->data, $event->config) );
    }

}




