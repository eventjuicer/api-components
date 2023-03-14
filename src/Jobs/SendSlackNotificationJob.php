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
        
        $url = env("SLACK_HOOK_ORG_" . $this->organizer_id);

        $response = (new Guzzle())->request("POST", $url, [
            "json" => [
                "text"=> $this->message,
            ]
        ]);

        $json = json_decode( (string) $response->getBody(), true ); 

        }catch(Exception $e) {

        }

    }
}