<?php

namespace Eventjuicer\Jobs\Meetups;

use Eventjuicer\Jobs\Job;
use Eventjuicer\Models\Meetup;
use Eventjuicer\Services\SaveOrder;
use Eventjuicer\Services\SparkPost;
use Eventjuicer\Services\Personalizer;
use Eventjuicer\Crud\CompanyData\Fetch as CompanyData;

class HandleP2CAgree extends Job //implements ShouldQueue
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


        // $order->setParticipant( $this->meetup->participant );
        // $order->makeVip( "C" . $this->meetup->company_id );

        $participant = new Personalizer( $this->meetup->participant );
        $substitution_data = $participant->getProfile(true);

        $companydata = $cd->toArray($this->meetup->company->data);
        $substitution_data["name"] = $companydata["name"];

        $mail->send([
            "template_id" => $this->meetup->organizer_id>1 ? "en-p2c-meetup-confirmed": "pl-p2c-meetup-confirmed",
            // "cc" => !empty($data["cc"]) ? $data["cc"] : false,
            // "bcc" => !empty($data["bcc"]) ? $data["bcc"] : false,
            "recipient" => [
                "name"  => $participant->translate("[[fname]] [[lname]]"),
                "email" => $participant->email
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