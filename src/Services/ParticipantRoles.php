<?php

namespace Eventjuicer\Services;

use Eventjuicer\Models\Participant;

class ParticipantRoles {

    protected $model;

    function __construct(Participant $model){
        $this->model = $model;
    }

    function toArray(){

        $this->model->load("purchases.tickets");

        return $this->model->purchases->filter(function($item){

            return $item->status != "cancelled" ;

        })->pluck("tickets")->collapse()->pluck("role")->all();

    }

    function hasRole(string $role){
        return in_array($role, $this->toArray());
    }

}