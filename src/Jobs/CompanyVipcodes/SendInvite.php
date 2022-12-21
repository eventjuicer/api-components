<?php

namespace Eventjuicer\Jobs\CompanyVipcodes;

use Eventjuicer\Jobs\Job;
//use Illuminate\Foundation\Bus\Dispatchable;
use Eventjuicer\Models\CompanyVipCode;
use Eventjuicer\Services\SparkPost;
use Eventjuicer\Crud\CompanyVipcodes\Create;
use Eventjuicer\Crud\CompanyData\Fetch as CompanyData;

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
    public function handle(SparkPost $mail, CompanyData $cd, Create $fetch){

        $code = $this->vipcode->code;
        $company = $this->vipcode->company;
        $organizer_id = $this->vipcode->organizer_id;
        $data = $cd->toArray(
            $cd->get($this->vipcode->company_id, "")
        );

        $template = "pl-vips-invite";

        $substitution_data = [
            "name" => $data["name"],
            "url" =>  "https://targiehandlu.pl/exhibitors/".$company->slug."?vipcode=".$code
        ];

        if($organizer_id > 1){
            
            $template = "ebe-vips-invite";

            $substitution_data = [
                "name" => $data["name"],
                "url" =>  "https://ecommerceberlin.com/exhibitors/".$company->slug."?vipcode=".$code
            ];

        }


        $mail->send([
            "template_id" => $template,
            "recipient" => [
                "name"  => $this->vipcode->email,
                "email" => $this->vipcode->email
            ],
            "substitution_data" => $substitution_data,                
            "locale" => "en"
        ]);

      
    }
}
