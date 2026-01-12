<?php

namespace Eventjuicer\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException; 

use Exception;
use Log;

class RunCompanySyncWithSecondaryDatabaseJob extends Job implements ShouldQueue {

    public $company_id;
    public $organizer_id;

    public function __construct($company_id = 0, $organizer_id = 0){
        $this->company_id = $company_id;
        $this->organizer_id = $organizer_id;
        
    }

    public function handle(){

        $URL = "https://sync.api.eventjuicer.com/api/companies/".$this->company_id."?skipQueueing=1";

        try {
            $response = (new Guzzle(["verify"=>false]))->request("GET", $URL);
        }
        catch (ClientException $e) {

            Log::error("RunCompanySyncWithSecondaryDatabaseJob",  [
                "response" => $e->getResponse(),
            ] );
        }
    }

}