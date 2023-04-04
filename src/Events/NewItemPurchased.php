<?php

namespace Eventjuicer\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Eventjuicer\Models\Participant as Model;

class NewItemPurchased extends Event implements ShouldBroadcast{

    use SerializesModels;

    public $data, $config;

    public function __construct(Model $data, array $config=[]){
        $this->data = $data;
        $this->config = $config;
    }

    public function handle(){
        
    }
    
    public function broadcastOn(){
        
        return new Channel("eventjuicer");
    }

    public function broadcastAs(){
        return 'NewItemPurchased';
    }

}
