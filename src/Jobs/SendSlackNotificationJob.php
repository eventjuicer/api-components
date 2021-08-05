<?php

namespace Eventjuicer\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use GuzzleHttp\Client as Guzzle;
use Exception;

class SendSlackNotificationJob extends Job implements ShouldQueue {

    protected $message;

    public function __construct($message = ""){
        $this->message = $message;
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