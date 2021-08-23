<?php

namespace Eventjuicer\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Eventjuicer\Resources\PublicPreBookingResource;
use Illuminate\Support\Collection;
use Eventjuicer\Models\Company;

class CompanyRelatedDataChanged extends Event implements ShouldBroadcast {

    use SerializesModels;

    public $model;
    public $user_id;

    public function __construct(Company $model, $user_id) {
       
        $this->model = $model;
        $this->user_id = $user_id;
    }


    public function handle(){

    }
    
    public function broadcastOn(){
        return new Channel("eventjuicer_admin");
    }

    public function broadcastAs(){
        return 'CompanyRelatedDataChanged';
    }

}
