<?php

namespace Eventjuicer\Jobs;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


use Illuminate\Support\Facades\Mail;
use App\Mail\PingWhenEmptyProfileEmail as Email;



use Eventjuicer\Models\Participant;
use Eventjuicer\Repositories\ParticipantDeliveryRepository;
use Eventjuicer\Services\Revivers\ParticipantSendable;

use Eventjuicer\Services\CompanyData;


class PingWhenEmptyProfileJob extends Job // implements ShouldQueue
{
    

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $participant, $eventId, $lang, $event_manager, $host;


    public function __construct(
        Participant $participant, 
        int $eventId, 
        string $lang="pl",
        string $event_manager,
        string $host
    ){
        
        $this->participant   = $participant;
        $this->eventId       = $eventId;
        $this->lang          = $lang;
        $this->event_manager = $event_manager;
        $this->host          = $host;

    }

    public function handle(
        ParticipantDeliveryRepository $deliveries, 
        ParticipantSendable $sendable, 
        CompanyData $cd
    ){

        //do we have company assigned?

        if(!$this->participant->company_id)
        {
            return;
        }

     
        //check for companydata fields freshness :)
        //check for required fields

        $errors = $cd->status($this->participant->company);

        if(!count($errors))
        {
            return;
        }

        $sendable->setMuteTime(20); //minutes!!!!

        // double check !

        if(! $sendable->filter( collect([])->push( $this->participant ), $this->eventId)->count() )
        {
            return;
        }


        Mail::send(
            new Email( 
                $this->participant, 
                $errors, 
                $this->lang, 
                $this->event_manager,
                $this->host
            )
        );

        //register COMPANY admin message?


        if(! env("MAIL_TEST", true))
        {
            $deliveries->updateAfterSend( $this->participant->email, $this->eventId );
        }

    }

}
