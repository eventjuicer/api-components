<?php

namespace Eventjuicer\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Eventjuicer\Resources\PublicPreBookingResource;
use Illuminate\Support\Collection;

class NewLockWasCreated extends Event implements ShouldBroadcast{

    use SerializesModels;

    public $data;
    public $uuid;

    public function __construct(Collection $locks, $uuid) {
       
        $this->data = PublicPreBookingResource::collection($locks)->toArray(app("request"));
        $this->uuid = $uuid;
    }


    public function handle(){

    }
    
    public function broadcastOn(){
        
        return new Channel("eventjuicer");
    }

    public function broadcastAs(){
        return 'NewLockWasCreated';
    }

}
