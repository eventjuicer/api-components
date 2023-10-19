<?php

namespace Eventjuicer\Jobs\Meetups;

use Eventjuicer\Jobs\Job;
use Eventjuicer\Models\Participant;
use Eventjuicer\Services\SparkPost;
use Eventjuicer\Services\Personalizer;
use Eventjuicer\Crud\CompanyMeetups\Fetch as CompanyMeetupsFetch;
use Carbon\Carbon;
use Log;

class HandleLTDAutoMassReject extends Job //implements ShouldQueue
{
    //use Dispatchable;

    /**
     *
     * @return void
     */

    public $participant;

    public function __construct(Participant $participant){
        $this->participant = $participant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CompanyMeetupsFetch $companymeetups, SparkPost $mail){


        //check if user has at least 2 accepted

        $allAgreed = $companymeetups->getAllAgreedForParticipants(
            collect([ $this->participant ])
        );

       
        Log::info("HandleLTDAutoMassReject", [
            "allAgreed" => $allAgreed->count()
        ]);

        if($allAgreed->count() >= 2){

           
            $getAllForParticipantsInPipeline = $companymeetups->getAllForParticipantsInPipeline(
                collect([ $this->participant ])
            );
    

                 
            Log::info("HandleLTDAutoMassReject", [
                "getAllForParticipantsInPipeline" => $getAllForParticipantsInPipeline->count()
            ]);

            foreach($getAllForParticipantsInPipeline as $toBeRejected){

                /** double protection */
                if($toBeRejected->agreed || $toBeRejected->responded_at){
                    continue;
                }
                       
                $toBeRejected->agreed = 0;
                $toBeRejected->responded_at =  Carbon::now("UTC");
                $toBeRejected->comment = "[autorejected] ". $toBeRejected->comment;
                $toBeRejected->save();

            }

            $participant = new Personalizer( $this->participant);
            $substitution_data = [];
            $substitution_data["fname"] = $this->participant->fname;
    
            
            $mail->send([
                "template_id" => $this->participant->organizer_id>1 ? "ebe-ltd-meetup-autorejected": "pl-ltd-meetup-autorejected",
                // "cc" => "workshops@targiehandlu.pl",
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
}