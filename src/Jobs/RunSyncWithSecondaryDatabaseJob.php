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

        $URL = $this->organizer_id > 1? "https://ecomm.berlin/api/sync": "https://app.ecommercewarsaw.com/api/sync";

        try {
            $response = (new Guzzle(["verify"=>false]))->request("GET", $URL."/".$this->participant_id);
        }
        catch (ClientException $e) {

            Log::error("RunSyncWithSecondaryDatabaseJob",  [
                "response" => $e->getResponse(),
            ] );
        }
    }

}