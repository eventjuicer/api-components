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

    protected $participant, $eventId;


    public function __construct(Participant $participant, int $eventId)
    {
        
        $this->participant = $participant;
        $this->eventId = $eventId;

    }

    public function handle(
        ParticipantDeliveryRepository $deliveries, 
        ParticipantSendable $sendable, 
        CompanyData $cd)
    {


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

        // double check !

        if(! $sendable->filter( collect([])->push( $this->participant ), $this->eventId)->count() )
        {
            return;
        }


        Mail::send(
            new Email( $this->participant, $errors )
        );


        if(! env("MAIL_TEST", true))
        {
            $deliveries->updateAfterSend( $this->participant->email, $this->eventId );
        }

    }

}
