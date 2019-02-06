<?php

namespace Eventjuicer\Jobs;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;


use Illuminate\Support\Facades\Mail;
use App\Mail\CompanyRepresentativesEmail as Email;



use Eventjuicer\Models\Participant;
use Eventjuicer\Repositories\ParticipantDeliveryRepository;
use Eventjuicer\Services\Revivers\ParticipantSendable;

use Eventjuicer\Services\CompanyData;


class CompanyRepresentativesJob extends Job // implements ShouldQueue
{
    

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $participant, $representatives, $eventId, $config;



    public function __construct(
        Participant $participant, 
        Collection $representatives,
        int $eventId,
        array $config){
        

        $this->participant   = $participant;
        $this->representatives  = $representatives;
        $this->eventId       = $eventId;
        $this->config = $config;

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

        // double check !

        if(! $sendable->filter( collect([])->push( $this->participant ), $this->eventId)->count() )
        {
            return;
        }


        Mail::send(
            new Email( 
                $this->participant, 
                $this->representatives,
                $this->config
            )
        );

        //register COMPANY admin message?

        if(! env("MAIL_TEST", true))
        {
            $deliveries->updateAfterSend( $this->participant->email, $this->eventId );
        }

    }

}
