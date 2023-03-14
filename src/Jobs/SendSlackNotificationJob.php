<?php

namespace Eventjuicer\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use GuzzleHttp\Client as Guzzle;
use Exception;

class SendSlackNotificationJob extends Job implements ShouldQueue {

    public $message;
    public $organizer_id;

    public function __construct($message = "", $organizer_id=0){
        $this->message = $message;
        $this->organizer_id = $organizer_id;
    }

    public function handle(){

        $env_name = "SLACK_HOOK_ORG_" . $this->organizer_id;

        $url = env( $env_name );

        if(empty($url)){
            throw new Exception("env $env_name missing");
        }

        $response = (new Guzzle(["verify"=>false]))->request("POST", $url, [
            "json" => [
                "text"=> $this->message,
            ]
        ]);

        $json = json_decode( (string) $response->getBody(), true ); 

      
    }
}