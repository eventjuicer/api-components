<?php

namespace Eventjuicer\Jobs\Meetups;

use Eventjuicer\Jobs\Job;
use Eventjuicer\Models\Meetup;
use Eventjuicer\Services\SparkPost;
use Eventjuicer\Services\Personalizer;
use Eventjuicer\Services\CompanyData;


class HandleLTDReject extends Job //implements ShouldQueue
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
    public function handle(SparkPost $mail, CompanyData $cd){

        $presenter = new Personalizer( $this->meetup->presenter );
        $participant = new Personalizer( $this->meetup->participant );
        $companydata = $cd->toArray($this->meetup->company);

        $substitution_data = [];
        $substitution_data["cname2"] = $companydata["name"];
        $substitution_data["fname"] = $participant->fname;
        $substitution_data["presenter"] = $presenter->presenter;
        $substitution_data["position"] = $presenter->position;
        $substitution_data["presentation_title"] = $presenter->presentation_title;
        $substitution_data["presentation_venue"] = $presenter->presentation_venue;
        $substitution_data["presentation_time"] = $presenter->presentation_time;

        $mail->send([
            "template_id" => $this->meetup->organizer_id>1 ? "ebe-ltd-rejected": "pl-ltd-meetup-rejected",
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