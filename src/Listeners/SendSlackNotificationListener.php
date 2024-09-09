<?php

namespace Eventjuicer\Listeners;

use Eventjuicer\Jobs\SendSlackNotificationJob;
use Eventjuicer\Events\NewItemPurchased;
// use Eventjuicer\Crud\Participants\ParticipantRoles;
use Eventjuicer\Crud\Participants\ParticipantTicketGroups;
use Eventjuicer\Services\Personalizer;


class SendSlackNotificationListener {


    public function __construct(){}

    public function handle(NewItemPurchased $event){    


        $webhooks = collect([]);
        $participant = $event->data;
        //$formdata = $event->config; (tickets, fields...)
        // $roles = new ParticipantRoles($event->data);
        $profile = new Personalizer($participant);
        $_ticketgroups = new ParticipantTicketGroups($participant);

        $tickets = $_ticketgroups->getTickets();
        $ticketgroups = $_ticketgroups->getGroups();

        $tickets->each(function($ticket) use ($webhooks){
            $webhooks->push($ticket->json);
        });

        $ticketgroups->each(function($ticketgroup) use ($webhooks){
            $webhooks->push($ticketgroup->json);
        });


        $webhooks->each(function($webhook) use($participant, $profile) {

            dispatch( new SendSlackNotificationJob( 

                $participant->email . " " . $profile->translate("[[fname]] [[lname]] [[cname2]]"),
                $participant->organizer_id,
                $webhook
    
            ) );

        });


      
    }


}




