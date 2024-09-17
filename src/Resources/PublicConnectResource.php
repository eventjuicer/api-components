<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PublicConnectResource extends Resource {


    public function toArray($request){
    
        $data = [];

        $data["id"] = $this->id;
        $data["event_id"] = (int) $this->event_id;
        $data["company_id"] = (int) $this->company_id;
        $data["important"] = (int) $this->important;
        $data["lang"] = (string) $this->lang;

        $data["tickets"] = PublicConnectTicketResource::collection($this->purchases->pluck("tickets")->collapse());
        
        //$this->purchases->pluck("tickets")->collapse();
        // $data["direction"] = (string) $this->direction;
        // $data["company_id"] = $this->company_id;
        // $data["participant_id"] = $this->participant_id;
        // $data["rel_participant_id"] = $this->rel_participant_id;
        // $data["agreed"] =  (int) $this->agreed;
        // $data["retries"] = (int) $this->retries;
        // $data["sent_at"] = (string) $this->sent_at;
        // $data["resent_at"] = (string) $this->resent_at;
        // $data["responded_at"] = (string) $this->responded_at;
        // $data["scheduled_at"] = (string) $this->scheduled_at;
        // $data["created_at"] = (string) $this->created_at;
        // $data["updated_at"] = (string) $this->updated_at;

   
        return $data;

            
    }
}