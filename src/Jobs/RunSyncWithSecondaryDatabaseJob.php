<?php

namespace Eventjuicer\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException; 

use Exception;
use Log;

class RunSyncWithSecondaryDatabaseJob extends Job implements ShouldQueue {

    public $participant_id;
    public $organizer_id;

    public function __construct($participant_id = 0, $organizer_id = 0){
        $this->participant_id = $participant_id;
        $this->organizer_id = $organizer_id;
        
    }

    public function handle(){

        $URL = "https://sync.api.eventjuicer.com/api/participants/".$this->participant_id;

        try {
            $response = (new Guzzle(["verify"=>false]))->request("GET", $URL);
        }
        catch (ClientException $e) {

            Log::error("RunSyncWithSecondaryDatabaseJob",  [
                "response" => $e->getResponse(),
            ] );
        }
    }

}