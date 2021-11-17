<?php

namespace Eventjuicer\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Eventjuicer\Resources\PublicPreBookingResource;
use Illuminate\Support\Model;

class NewItemPurchased extends Event implements ShouldBroadcast{

    use SerializesModels;

    public $data;

    public function __construct(Model $participant){
        $this->data = $participant;
    }

    public function handle(){}
    
    public function broadcastOn(){
        
        return new Channel("eventjuicer");
    }

    public function broadcastAs(){
        return 'NewItemPurchased';
    }

}
