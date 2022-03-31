<?php

namespace Eventjuicer\Jobs\CompanyVipcodes;

use Eventjuicer\Jobs\Job;
//use Illuminate\Foundation\Bus\Dispatchable;
use Eventjuicer\Models\CompanyVipCode;
use Eventjuicer\Services\SparkPost;
use Eventjuicer\Crud\CompanyVipcodes\Create;


class SendInvite extends Job //implements ShouldQueue
{
    //use Dispatchable;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $vipcode;

    public function __construct(CompanyVipCode $vipcode){
        $this->vipcode = $vipcode;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SparkPost $mail, Create $fetch){

        $code = $this->vipcode->code;

        $mail->send([
            "template_id" => "admin-report-message",
            "recipient" => [
                "name"  => "adam zygadlewicz",
                "email" => "adam@zygadlewicz.com"
            ],
            "substitution_data" => [
                "code" => $code
            ],                
            "locale" => "en"
        ]);

       

        // event(new RestrictedImageUploaded( $cdn ));
      
    }
}
