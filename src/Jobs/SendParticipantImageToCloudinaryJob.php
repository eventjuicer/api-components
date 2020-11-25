<?php

namespace Eventjuicer\Jobs;


use Illuminate\Contracts\Queue\ShouldQueue;
//use Illuminate\Foundation\Bus\Dispatchable;
use Eventjuicer\Models\Participant as Model;
use Eventjuicer\Models\ParticipantFields;
use Eventjuicer\Services\Cloudinary;
use Eventjuicer\Services\Personalizer;
use Carbon\Carbon;


class SendParticipantImageToCloudinaryJob extends Job implements ShouldQueue {
    //use Dispatchable;

    public $participant;

    public $fieldsToSend = [

        "logotype" => 255, // logotype_cdn
        "avatar" => 254 // avatar_cdn
    ];



    public function __construct(Model $participant)
    {
        $this->participant = $participant;
    }


    public function handle( Cloudinary $image ){

        //TEMP 


        $profile = (new Personalizer( $this->participant ))->getProfile();

        $toBeProcessed = array_intersect_key($profile, $this->fieldsToSend);

        foreach($toBeProcessed as $name => $url)
        {

            if(stristr($url, "http") === false)
            {
                continue;
            }

            $pubName  = "p_" . $this->participant->id . "_" . $name;

            $response = $image->upload($url, $pubName);

            if(!empty($response) && !empty($response["secure_url"])){

                //update fieldpivot

                $targetFieldPivot = $this->fieldsToSend[$name];

                ParticipantFields::updateOrCreate([
                    "participant_id" =>$this->participant->id,
                    "field_id" => $targetFieldPivot
                ], [
                    "participant_id" =>$this->participant->id,
                    "field_id" => $targetFieldPivot,
                    "field_value" => $response["secure_url"],
                    "organizer_id" => $this->participant->organizer_id,
                    "group_id" => $this->participant->group_id,
                    "event_id" => $this->participant->event_id,
                    "archive" => "",
                    "updatedon" => (string) Carbon::now()->toDateTimeString()
                ]);

            }
            

        }
        
    }
}


