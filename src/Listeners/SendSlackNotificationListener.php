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
            $webhooks->push( trim($ticket->json) );
        });

        $ticketgroups->each(function($ticketgroup) use ($webhooks){
            /**
             * WARNING! ticket group may be not assigned!
             */
            if($ticketgroup){
                $webhooks->push( trim($ticketgroup->json) );
            }
        });


        $webhooks->filter()->unique()->each(function($webhook) use($participant, $profile) {

            dispatch( new SendSlackNotificationJob( 

                $participant->email . " " . $profile->translate("[[fname]] [[lname]]; [[position]] @ [[cname]] [[cname2]]; [[phone]]"),
                $participant->organizer_id,
                $webhook
    
            ) );

        });


      
    }


}




