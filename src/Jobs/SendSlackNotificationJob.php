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
    public $webhook;

    public function __construct($message = "", $organizer_id=0, $webhook=""){
        $this->message = $message;
        $this->organizer_id = intval($organizer_id);
        $this->webhook = trim($webhook);
    }

    public function handle(){

        if(empty($this->webhook) || $this->webhook === "default"){
            /**
             * TODO
             * we shoud get default webhook from organizer_db
             */
            $env_name = "SLACK_HOOK_ORG_" . $this->organizer_id;
            $url = env( $env_name );
        }else{
            $url = $this->webhook;
        }

        if(empty($url) || stripos($url, "https")===false){
            throw new Exception("Webhook URL missing or invalid");
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