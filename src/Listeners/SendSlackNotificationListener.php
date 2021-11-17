<?php

namespace Eventjuicer\Listeners;

use Eventjuicer\Jobs\SendSlackNotificationJob;
use Eventjuicer\Events\NewItemPurchased;

class SendSlackNotificationListener {


    public function __construct(){}

    public function handle(NewItemPurchased $event){    

        dispatch( new SendSlackNotificationJob( 

            json_encode($event->data->email)

        ) );
    }


}




