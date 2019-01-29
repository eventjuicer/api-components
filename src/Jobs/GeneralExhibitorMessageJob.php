<?php

namespace Eventjuicer\Jobs;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


use Illuminate\Support\Facades\Mail;
use App\Mail\GeneralExhibitorEmail as Email;


use Eventjuicer\Models\Participant;
use Eventjuicer\Repositories\ParticipantDeliveryRepository;
use Eventjuicer\Services\Revivers\ParticipantSendable;
 

class GeneralExhibitorMessageJob extends Job // implements ShouldQueue
{
    

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $participant, $eventId;
    public $subject, $view, $event_manager, $viewlang, $lang, $domain;

    public function __construct(Participant $participant, int $eventId, array $config)
    {
        
        $this->participant = $participant;
        $this->eventId = $eventId;

        $this->view = array_get($config, "email");
        $this->viewlang = array_get($config, "viewlang");

        $this->subject = array_get($config, "subject", "Organizacyjnie...");
        $this->event_manager = array_get($config, "event_manager", "");
        $this->lang = array_get($config, "lang", "en");
        $this->domain = array_get($config, "domain", "");
    }

    public function handle(
        ParticipantDeliveryRepository $deliveries, 
        ParticipantSendable $sendable)
    {


        //do we have company assigned?

        if(!$this->participant->company_id)
        {
            return;
        }

        $sendable->setMuteTime(20); //minutes!!!!

        // double check !

        if(! $sendable->filter( collect([])->push( $this->participant ), $this->eventId)->count() )
        {
            return;
        }


        Mail::send( new Email( 
                $this->participant, 
                $this->subject, 
                $this->view,
                $this->viewlang,
                $this->lang,
                $this->event_manager,
                $this->domain
        ));


        if(! env("MAIL_TEST", true))
        {
            $deliveries->updateAfterSend( $this->participant->email, $this->eventId );
        }

    }

}
