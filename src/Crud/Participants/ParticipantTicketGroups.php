<?php

namespace Eventjuicer\Crud\Participants;

use Eventjuicer\Models\Participant;

class ParticipantTicketGroups {

    protected $model, $tickets, $groups;

    function __construct(Participant $model){

        $this->model = $model;

        if(!$this->model->relationLoaded("purchases")){
            $this->model->load("purchases.tickets.group");
        }

        $this->tickets = $this->model->purchases->filter(function($item){

            return $item->status != "cancelled" ;

        })->pluck("tickets")->collapse();

        $this->groups = $this->tickets->pluck("group");

    }

    function getTickets(){
        return $this->tickets;
    }

    function getGroups(){
        return $this->groups;
    }

    


}