<?php

namespace Eventjuicer\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;
// use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Eventjuicer\Models\CompanyData;

class RestrictedImageUploaded extends Event implements ShouldBroadcast{

    use SerializesModels;

    public $data;

    public function __construct(CompanyData $model){
        $this->data = $model;
    }

    public function handle(){}
    
    public function broadcastOn(){
        
        return new Channel("eventjuicer");
    }

    public function broadcastAs(){
        return 'RestrictedImageUploaded';
    }

}
