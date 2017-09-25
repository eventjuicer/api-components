<?php

namespace Eventjuicer\Services\Sender\Jobs;

use Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;


use Illuminate\Contracts\Mail\Mailer;

use Eventjuicer\SenderCampaign;
use Eventjuicer\SenderImport;
use Eventjuicer\SenderDelivery;

//use Log;




class PrepareDelivery extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $campaign;

    public function __construct(SenderCampaign $campaign)
    {

        set_time_limit(6000);

        $this->campaign = $campaign;
       
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        dd($this->campaign->with("includes.emails") );

       
        $emails = SenderImport::find($this->import_id)->emails;








        foreach($emails AS $email)
        {
            
            

            $delivery               = new SenderDelivery;

            $deliver->campaign_id   = $this->campaign->id;
            $deliver->organizer_id  = $this->campaign->organizer_id;

            $email->deliveries()->save($delivery);
        }

        $this->campaign->imports()->updateExistingPivot($this->import_id, ["prepared" => 1]); 

        // Log::error("UploadFileToS3 failed! Local image file does not exist!", ["image_id" => $this->image->id ]);
      

    }






}