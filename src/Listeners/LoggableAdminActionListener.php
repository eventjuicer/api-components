<?php

namespace Eventjuicer\Listeners;

use Eventjuicer\Jobs\SendSlackNotificationJob;
use Eventjuicer\Events\Event;
use Eventjuicer\Services\UserLog;

class LoggableAdminActionListener {

    private $userlog;

    public function __construct(UserLog $userlog){
        $this->userlog = $userlog;
    }

    public function handle(Event $event){    
        $this->userlog->create($event->model, $event->user_id);
    }


}




