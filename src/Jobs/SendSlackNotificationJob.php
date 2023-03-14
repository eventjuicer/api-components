<?php

namespace Eventjuicer\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use GuzzleHttp\Client as Guzzle;
use Exception;



class SendSlackNotificationJob extends Job  {

    protected $message;
    protected $organizer_id;

    public function __construct($message = "", $organizer_id=0){
        $this->message = $message;
        $this->organizer_id = $organizer_id;
    }

    public function handle(){

        $url = env("SLACK_HOOK_ORG_" . $this->organizer_id);

        if(empty($url)){
            throw new \Exception("SLACK_HOOK_ORG_ missing");
        }

        $response = (new Guzzle(["verify"=>false]))->request("POST", $url, [
            "json" => [
                "text"=> $this->message,
            ]
        ]);

        $json = json_decode( (string) $response->getBody(), true ); 

      
    }
}