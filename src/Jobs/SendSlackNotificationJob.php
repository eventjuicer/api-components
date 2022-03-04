<?php

namespace Eventjuicer\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use GuzzleHttp\Client as Guzzle;
use Exception;

class SendSlackNotificationJob extends Job implements ShouldQueue {

    protected $message;
    protected $organizer_id;

    public function __construct($message = "", $organizer_id=1){
        $this->message = $message;
        $this->organizer_id = $organizer_id;
    }

    public function handle(){

    
        try{

        $response = (new Guzzle())->request("POST", env("SLACK_HOOK_TEH"), [
            "json" => ["text"=> $this->message]]);

        $json = json_decode( (string) $response->getBody(), true ); 

        }catch(Exception $e) {

        }

    }
}