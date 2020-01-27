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

    public $participant, $eventId, $config;

    public function __construct(Participant $participant, int $eventId, array $config)
    {
        
        $this->participant      = $participant;
        $this->eventId          = $eventId;
        $this->config           = $config;
       
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
                $this->config
        ));


        if(! env("MAIL_TEST", true))
        {
            $deliveries->updateAfterSend( $this->participant->email, $this->eventId );
        }

    }

}
