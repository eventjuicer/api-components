<?php

namespace Eventjuicer\Jobs\Meetups;

use Eventjuicer\Jobs\Job;
use Eventjuicer\Models\Meetup;
use Eventjuicer\Services\SparkPost;
use Eventjuicer\Services\Personalizer;
use Eventjuicer\Services\Company\GetCompanyDataValue;


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
    public function handle(SparkPost $mail){

        $companydata = new GetCompanyDataValue($this->meetup->company);
        $presenter = new Personalizer( $this->meetup->presenter );
        $participant = new Personalizer( $this->meetup->participant );

        $ltd_reject_template = $companydata->get("ltd_reject_template", "");
        $cname2 = $companydata->get("name");

        $substitution_data = [];
        $substitution_data["cname2"] = $cname2;
        $substitution_data["fname"] = $participant->fname;
        $substitution_data["presenter"] = $presenter->presenter;
        $substitution_data["position"] = $presenter->position;
        $substitution_data["presentation_title"] = $presenter->presentation_title;
        $substitution_data["presentation_venue"] = $presenter->presentation_venue;
        $substitution_data["presentation_time"] = $presenter->presentation_time;

        $substitution_data["additional_message"] = $ltd_reject_template;

        
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

        

      
    }
}