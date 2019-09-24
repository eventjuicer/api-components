<?php

namespace Eventjuicer\Jobs;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Mail;
use App\Mail\CallForPapersEmail as Email;

use Eventjuicer\Models\Participant;
use Eventjuicer\Repositories\ParticipantDeliveryRepository;
use Eventjuicer\Services\Revivers\ParticipantSendable;
 

class CallForPapersMessageJob extends Job // implements ShouldQueue
{
    

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $participant, $eventId;
    public $subject, $view, $votes;

    public function __construct(Participant $participant, int $eventId, array $config)
    {
        
        $this->participant = $participant;
        $this->eventId = $eventId;
        $this->view = array_get($config, "email");
        $this->subject = array_get($config, "subject", "");
        $this->votes = array_get($config, "votes", 0);

    }

    public function handle(
        ParticipantDeliveryRepository $deliveries, 
        ParticipantSendable $sendable)
    {

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
                $this->votes
        ));


        if(! env("MAIL_TEST", true))
        {
            $deliveries->updateAfterSend( $this->participant->email, $this->eventId );
        }

    }

}
