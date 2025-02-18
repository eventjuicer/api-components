<?php

namespace Eventjuicer\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use Eventjuicer\Models\Meetup;

class ParticipantIsToBeUpgraded extends Event {

    use SerializesModels;

    public $meetup;

    public function __construct(Meetup $meetup){
        $this->meetup = $meetup;
    }

    public function handle(){
        
    }
    
    // public function broadcastOn(){
        
    //     return new Channel("eventjuicer");
    // }

    // public function broadcastAs(){
    //     return 'NewItemPurchased';
    // }

}
