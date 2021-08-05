<?php

namespace Eventjuicer\Listeners;

use Eventjuicer\Jobs\SendSlackNotificationJob;
use Eventjuicer\Events\NewLockWasCreated;

class SendSlackNotificationListener {


    public function __construct(){}

    public function handle(NewLockWasCreated $event){    

        $locks = $event->data; //array!        

        dispatch( new SendSlackNotificationJob( 

            json_encode($locks)

        ) );
    }


}




