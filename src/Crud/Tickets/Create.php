<?php

namespace Eventjuicer\Crud\Tickets;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\Ticket;
use Eventjuicer\Repositories\EloquentTicketRepository;
use Illuminate\Validation\Rule;
use Carbon\Carbon;


class Create extends Crud  {

    protected $repo;
    
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

    public function create(){

        if(!$this->validates()){
            return null;
        }

        $data = $this->getData("Cloned - ");


        $this->repo->saveModel($data);

        return $this->find( $this->repo->getId() );
    }


/**
     
    {
    "internal_name": "asdasdasdad",
    "translation_asset_id": "organizer",
    "baseprice": 0,
    "price_currency": "PLN",
    "start": "2025-09-06 00:00:00",
    "end": "2025-10-25 00:00:00",
    "limit": 10000,
    "max": 0,
    "role": "",
    "ticket_group_id": 0
}

*/

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

        // Validate: limit must be > 0
        if ($limit <= 0) {
            $limit = 100;
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


    protected function getData($prefix =""){
        
        $legacy_name =  (string) $prefix . $this->getParam("_name", "New Ticket");
      
        $ticket_group_id = (int) $this->getParam("ticket_group_id", 0);
        $event_id = (int) $this->getParam("event_id", 0);
        $group_id = (int) $this->getParam("group_id", 0);
        $organizer_id = (int) $this->getParam("organizer_id", 0);

        $delayed = (int) $this->getParam("delayed", 0);
        $featured = (int) $this->getParam("featured", 0);
        $internal = (int) $this->getParam("internal", 0);
        
        $limit = (int) $this->getParam("limit", 0);
        $max = (int) $this->getParam("max", 0);

        $names = [
            "en" => $legacy_name,
            "pl" => $legacy_name,
            "de" => $legacy_name,
        ];
        $descriptions = $this->getParam("descriptions", []);
        $price = $this->getParam("price",  $this->getParam("_price", [
            "en"=>0,
            "de"=>0,
            "pl"=>0
        ]));
      
        $internal_name = $this->getParam("internal_name",  $legacy_name);
        $translation_asset_id = $this->getParam("translation_asset_id",  $legacy_name);
        $image = $this->getParam("image", "");
        $thumbnail = $this->getParam("thumbnail", "");
        $start = $this->getParam("start", "");
        $end = $this->getParam("end", "");
        $role = $this->getParam("role", "");
        $details_url = $this->getParam("details_url", "");
        $ns = $this->getParam("ns", "");
        $additional_recipients = $this->getParam("additional_recipients", "");
        $additional_message = $this->getParam("additional_message", "");

        

        $baseprice = (int) $this->getParam("baseprice", 0);
        $price_currency = $this->getParam("price_currency", "");

        return compact(
            "ticket_group_id",
            "event_id",
            "group_id",
            "organizer_id",

            "delayed",
            "featured",
            "internal",
            
            "limit",
            "max",
            
            "names",
            "descriptions",
            "price",

            "internal_name",
            "translation_asset_id",
            "image",
            "thumbnail",
            "start",
            "end",
            "role",
            "details_url",
            "ns",
            "additional_recipients",
            "additional_message",

            "baseprice",
            "price_currency"
        );
    }



}


