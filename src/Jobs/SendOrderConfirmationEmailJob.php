<?php

namespace Eventjuicer\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Eventjuicer\Models\Participant;
use Eventjuicer\Services\Personalizer;
use Eventjuicer\Services\SparkPost;
use Exception;
use Log;

class SendOrderConfirmationEmailJob extends Job implements ShouldQueue {

    public $participant;
    public $config;

    public function __construct(Participant $participant, array $config=[]){
        $this->participant = $participant;
        $this->config = $config;
    }

    public function handle( SparkPost $mail ){

        $personalizer = new Personalizer($this->participant);
        $profile = $personalizer->getProfile(true);

        if($this->participant->group_id > 1){
            $domain = "ecommerceberlin.com";

        }else{
            $domain = "targiehandlu.pl";
        }

        $profile["link"] = 'https://'.$domain.'/tickets/' . $profile["hash"];
        $profile["ticket"] = 'https://'.$domain.'/tickets/' . $profile["hash"];
        $profile["login"] = 'https://'.$domain.'/recall/' . $profile["token"];
        $profile["as_array"] = $personalizer->getProfileArray();

     
        $mail->send([
            "template_id" => array_get($this->config, "template", false),
            "cc" => array_get($this->config, "cc", false),
            "bcc" =>array_get($this->config, "bcc", false),
            "recipient" => [
                "name"  => $personalizer->translate("[[fname]] [[lname]]"),
                "email" => $this->participant->email
            ],
            "substitution_data" => $profile,                
            "locale" => array_get($this->config, "locale", "en")
        ]);
        


    }

   
}