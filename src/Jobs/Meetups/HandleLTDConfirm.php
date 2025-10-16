<?php

namespace Eventjuicer\Jobs\Meetups;

use Eventjuicer\Jobs\Job;
use Eventjuicer\Models\Meetup;
use Eventjuicer\Services\SaveOrder;
use Eventjuicer\Services\SparkPost;
use Eventjuicer\Services\Personalizer;
use Eventjuicer\Crud\CompanyData\Fetch as CompanyData;
use GuzzleHttp\Client as Guzzle;


class HandleLTDConfirm extends Job //implements ShouldQueue
{
    //use Dispatchable;

    /**
     *
     * @return void
     */

    public $meetup;

    public function __construct(Meetup $meetup){
        $this->meetup = $meetup;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SaveOrder $order, SparkPost $mail, CompanyData $cd){

        //DO NOT MAKE VIP on EBE
        if(intval($this->meetup->organizer_id)===1){
            $order->setParticipant( $this->meetup->participant );
            $order->makeVip( "C" . $this->meetup->company_id );
        }

        $presenter = new Personalizer( $this->meetup->presenter );
        $participant = new Personalizer( $this->meetup->participant );
        $companydata = $cd->toArray($this->meetup->company->data);

        $substitution_data = [];
        $substitution_data["cname2"] = $companydata["name"];
        $substitution_data["fname"] = $participant->fname;
        $substitution_data["presenter"] = $presenter->presenter;
        $substitution_data["presentation_title"] = $presenter->presentation_title;
        $substitution_data["presentation_venue"] = $presenter->presentation_venue;
        $substitution_data["presentation_time"] = $presenter->presentation_time;
        $substitution_data["presentation_day"] = $presenter->presentation_day;
        $substitution_data["tm_visitday"] = $presenter->tm_visitday? $presenter->tm_visitday: $presenter->presentation_day;

        try {
            $response = (new Guzzle(["verify"=>false]))->request("POST", "https://ecommercewarsaw.com/api/email", [
                "json" => [
                    "token" => env("EXTAPI_TOKEN"),
                    "substitution_data" => $substitution_data,
                    "reason" => "confirm",
                    "recipient_id" => $participant->id   
                ]
            ]);
        }catch(\Exception $e){

        }


        // $mail->send([
        //     "template_id" => $this->meetup->organizer_id>1 ? "ebe-ltd-confirmed": "pl-ltd-meetup-confirmed",
        //     // "cc" => "workshops@targiehandlu.pl",
        //     // "bcc" => !empty($data["bcc"]) ? $data["bcc"] : false,
        //     "recipient" => [
        //         "name"  => $participant->translate("[[fname]] [[lname]]"),
        //         "email" => $participant->email
        //     ],
        //     "substitution_data" => $substitution_data,             
        //     "locale" => "pl"//$locale
        // ]);




      
    }
}