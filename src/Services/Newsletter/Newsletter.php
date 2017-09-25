<?php

namespace Eventjuicer\Services\Newsletter;


use Contracts\Newsletter AS INewsletter;
use Contracts\Context;
use Contracts\Setting;

use App;

use Illuminate\Foundation\Bus\DispatchesJobs;

use Jobs\SendNewsletterWelcomeEmail;

use Eventjuicer\Services\Repository;

class Newsletter implements INewsletter {
	
	protected $context;
	protected $setting;
    protected $participants;

    use DispatchesJobs;


	function __construct(Context $context, Setting $setting, Repository $participants)
	{

		 $this->context = $context->level();
		 $this->setting = $setting;

         $this->participants = $participants;
	}

    function confirm($token)
    {

        $participant = $this->participants->firstOrCreate(

            ["token" => $token]
        );

        if($participant)
        {
            $this->participants->updateFlags(["confirmed" => 1], $participant->id);

            return $participant;
        }

        return false;

    }

	function subscribe($email = "", $widget_id = 1)
	{

        $participant = $this->participants->firstOrCreate(

            ["email" => $email]
        );

        if($participant)
        {
            $this->dispatchNow(new SendNewsletterWelcomeEmail($participant, $widget_id));

            return $participant->id;
        }

        return false;
	}

}