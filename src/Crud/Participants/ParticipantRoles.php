<?php

namespace Eventjuicer\Crud\Participants;

use Eventjuicer\Models\Participant;

class ParticipantRoles {

    protected $model, $roles;

    function __construct(Participant $model){

        $this->model = $model;

        if(!$this->model->relationLoaded("purchases")){
            $this->model->load("purchases.tickets");
        }

        $this->roles = $this->model->purchases->filter(function($item){

            return $item->status != "cancelled" ;

        })->pluck("tickets")->collapse()->pluck("role")->all();

    }

    function toArray(){
        return $this->roles;
    }

    function hasRole(string $role){
        return in_array($role, $this->roles);
    }

    function __toString(){
        return implode(", ", $this->roles);
    }

}