<?php

namespace Eventjuicer\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException; 

use Exception;
use Log;

class RunSyncWithSecondaryDatabaseJob extends Job implements ShouldQueue {

    public $participant_id;
    protected $URL = "https://ecomm.berlin/api/sync";

    public function __construct($participant_id=0){
        $this->participant_id = $participant_id;
    }

    public function handle(){

        try {
            $response = (new Guzzle(["verify"=>false]))->request("GET", $this->URL."/".$this->participant_id);
        }
        catch (ClientException $e) {

            Log::error("RunSyncWithSecondaryDatabaseJob",  [
                "response" => $e->getResponse(),
            ] );
        }
    }

}