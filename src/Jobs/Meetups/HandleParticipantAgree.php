<?php

namespace Eventjuicer\Jobs\Meetups;

use Eventjuicer\Jobs\Job;
use Eventjuicer\Models\Meetup;
use Eventjuicer\Services\SaveOrder;
use Eventjuicer\Services\SparkPost;
use Eventjuicer\Services\Personalizer;
use Eventjuicer\Crud\CompanyData\Fetch as CompanyData;

class HandleParticipantAgree extends Job //implements ShouldQueue
{
    //use Dispatchable;

    /**
     * Create a new job instance.
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


        $order->setParticipant( $this->meetup->participant );
        $order->makeVip( "C" . $this->meetup->company_id );

        $personalizer = new Personalizer( $this->meetup->participant );
        $substitution_data = $personalizer->getProfile(true);

        $companydata = $cd->toArray($this->meetup->company->data);
        $substitution_data["name"] = $companydata["name"];

        $mail->send([
            "template_id" => "pl-p2c-meetup-confirmed",
            // "cc" => !empty($data["cc"]) ? $data["cc"] : false,
            // "bcc" => !empty($data["bcc"]) ? $data["bcc"] : false,
            "recipient" => [
                "name"  => $personalizer->translate("[[fname]] [[lname]]"),
                "email" => $personalizer->email
            ],
            "substitution_data" => $substitution_data,             
            "locale" => "pl"//$locale
        ]);



        // $substitution_data = [
        //     "name" => $data["name"],
        //     "url" =>  "https://targiehandlu.pl/exhibitors/".$company->slug."?vipcode=".$code
        // ];

        // $mail->send([
        //     "template_id" => "pl-vips-invite",
        //     "recipient" => [
        //         "name"  => $this->vipcode->email,
        //         "email" => $this->vipcode->email
        //     ],
        //     "substitution_data" => $substitution_data,                
        //     "locale" => "en"
        // ]);

      
    }
}