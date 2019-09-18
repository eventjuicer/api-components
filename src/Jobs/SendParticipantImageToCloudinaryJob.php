<?php

namespace Eventjuicer\Jobs;


use Illuminate\Contracts\Queue\ShouldQueue;
//use Illuminate\Foundation\Bus\Dispatchable;

use Eventjuicer\Models\Participant as Model;
use Eventjuicer\Models\ParticipantFields;

use Eventjuicer\Services\Cloudinary;
use Eventjuicer\Repositories\ParticipantRepository;



use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Services\Personalizer;
use Carbon\Carbon;


class SendParticipantImageToCloudinaryJob extends Job //implements ShouldQueue
{
    //use Dispatchable;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $participant;

    public $fieldsToSend = [

        "logotype" => 255,
        "avatar" => 254
    ];



    public function __construct(Model $participant)
    {
        $this->participant = $participant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle( Cloudinary $image ){


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
                    "group_id" => $this->participant->organizer_id,
                    "event_id" => $this->participant->event_id,
                    "archive" => "",
                    "updatedon" => (string) Carbon::now()->toDateTimeString()
                ]);

            }
            

        }
        
    }
}

/*

array:16 [
  "public_id" => "p_97790_avatar"
  "version" => 1568766134
  "signature" => "37c0d8ae469eb76a3d73f91356fefd1586605d6a"
  "width" => 640
  "height" => 544
  "format" => "jpg"
  "resource_type" => "image"
  "created_at" => "2019-09-18T00:22:14Z"
  "tags" => []
  "bytes" => 54969
  "type" => "upload"
  "etag" => "f4a19d6b10d16c3d74af0fc98da20995"
  "placeholder" => false
  "url" => "http://res.cloudinary.com/eventjuicer/image/upload/v1568766134/p_97790_avatar.jpg"
  "secure_url" => "https://res.cloudinary.com/eventjuicer/image/upload/v1568766134/p_97790_avatar.jpg"
  "original_filename" => "Donatas-34"
]

*/
