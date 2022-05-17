<?php

namespace Eventjuicer\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use GuzzleHttp\Client as Guzzle;
use Exception;


/**

https://fp20.slack.com/apps/A0F7XDUAZ-incoming-webhooks?tab=more_info

**/

class SendSlackNotificationJob extends Job implements ShouldQueue {

    protected $message;
    protected $organizer_id;

    public function __construct($message = "", $organizer_id=0){
        $this->message = $message;
        $this->organizer_id = $organizer_id;
    }

    public function handle(){

        try{
        
        $response = (new Guzzle())->request("POST", env("SLACK_HOOK"), [
            "json" => [
                "text"=> $this->message,
                "channel"=> env("SLACK_CHANNEL_ORG" . $this->organizer_id)
            ]
        ]);

        $json = json_decode( (string) $response->getBody(), true ); 

        }catch(Exception $e) {

        }

    }
}