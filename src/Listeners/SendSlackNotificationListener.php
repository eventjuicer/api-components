<?php

namespace Eventjuicer\Listeners;

use Eventjuicer\Jobs\SendSlackNotificationJob;
use Eventjuicer\Events\NewItemPurchased;

class SendSlackNotificationListener {


    public function __construct(){}

    public function handle(NewItemPurchased $event){    

        $participant = $event->data;

        if($participant->organizer_id > 1){
            return;
        }

        dispatch( new SendSlackNotificationJob( 

            json_encode($participant->email)

        ) );
    }


}




