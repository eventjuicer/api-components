<?php

namespace Eventjuicer\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException; 

use Exception;
use Log;

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

        try {
            $response = (new Guzzle(["verify"=>false]))->request("POST", $url, [
                "json" => [
                    "text"=> $this->message,
                ]
            ]);
        }
        catch (ClientException $e) {
     
            // $json = json_decode( (string) $response->getBody(), true ); 

            Log::error("SendSlackNotificationJob",  [
                "env_name" => $env_name,
                "response" => $e->getResponse(),
                "response2"=> $response->getBody()->getContents()
            ] );
            

        }

    }

   
}