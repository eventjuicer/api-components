<?php

namespace Eventjuicer\Crud\Tickets;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\Ticket;
use Eventjuicer\Repositories\EloquentTicketRepository;
use Illuminate\Validation\Rule;
use Carbon\Carbon;


class Create extends Crud  {

    public $repo;
    
    function __construct(EloquentTicketRepository $repo){
        $this->repo = $repo;
    }

    function validates(){
        
        return $this->isValid([
            "event_id" => "bail|required|numeric|min:1|digits_between:1,9",
            "group_id" =>  "bail|required|numeric|min:1|digits_between:1,9",
            "organizer_id" =>  "bail|required|numeric|min:1|digits_between:1,9"
        ]);

    }

    public function update($id){

        $ticket = Ticket::find($id);
        if(!$ticket){
            return null;
        }
        //inherit organizer, group, event from ticket!

        $translation_asset_id = trim($this->getParam("translation_asset_id", ""));
        $internal_name = trim($this->getParam("internal_name", ""));
        $baseprice = intval($this->getParam("baseprice", 0));
        $limit = intval($this->getParam("limit", 100));
        $max = intval($this->getParam("max", 0));
        $role = trim($this->getParam("role", ""));
        $ticket_group_id = intval($this->getParam("ticket_group_id", 0));

        // Validate: baseprice must be >= 0
        if ($baseprice < 0) {
            $baseprice = 0;
        }

        // Validate: limit must be > 0 and <= 65535 (smallint unsigned)
        if ($limit <= 0) {
            $limit = 100;
        }
        if ($limit > 65535) {
            $limit = 65535;
        }

        // Validate: max must be >= 0 and <= 255 (tinyint unsigned)
        if ($max < 0) {
            $max = 0;
        }
        if ($max > 255) {
            $max = 255;
        }

        //merge with new data
        $ticket->ticket_group_id = $ticket_group_id;
        $ticket->role = $role;

        $ticket->translation_asset_id = $translation_asset_id;
        // Allow internal_name to be empty - transformer will handle fallback to _name
        $ticket->internal_name = $internal_name;

        $ticket->baseprice = $baseprice;
        $ticket->price_currency = strtoupper($this->getParam("price_currency", ""));

        $ticket->price = [
            "en" => $baseprice,
            "de" => $baseprice,
            "pl" => $baseprice,
        ];

        $ticket->names = [  
            "en" => $internal_name,
            "de" => $internal_name,
            "pl" => $internal_name,
        ];

        $ticket->start = Carbon::parse($this->getParam("start", ""))->format("Y-m-d H:i:s");
        $ticket->end = Carbon::parse($this->getParam("end", ""))->format("Y-m-d H:i:s");
        $ticket->limit = $limit;
        $ticket->max = $max;
        $ticket->paid = $baseprice > 0 ? 1 : 0;

        $ticket->ns = "";
        $ticket->additional_recipients = "";
        $ticket->additional_message = "";

        $ticket->save();

        return $ticket->fresh();
    }

    public function delete($id){

        // $this->repo->update(["disabled"=>1], $id);
        // return $this->find($id);
    }


    protected function getData(){
        
    }



}


