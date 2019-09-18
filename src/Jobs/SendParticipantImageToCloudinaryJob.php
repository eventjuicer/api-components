<?php

namespace Eventjuicer\Jobs;


use Illuminate\Contracts\Queue\ShouldQueue;
//use Illuminate\Foundation\Bus\Dispatchable;

use Eventjuicer\Models\Participant as Model;
use Eventjuicer\Services\Cloudinary;
use Eventjuicer\Repositories\ParticipantRepository;



use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Services\Personalizer;




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

        "logotype",
        "avatar"
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

        $toBeProcessed = array_intersect_key($profile, array_flip($this->fieldsToSend));

        foreach($toBeProcessed as $name => $url)
        {

            if(stristr($url, "http") === false)
            {
                continue;
            }

            $pubName  = "p_" . $this->participant->id . "_" . $name;

            $response = $image->upload($url, $pubName);

            if(!empty($response)){

                dd($response);
            }
            

        }
        
    }
}
