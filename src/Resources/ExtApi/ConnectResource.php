<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Hashids;

class ConnectResource extends Resource {


    public function toArray($request){
    
        $data = [];

        $tickets =$this->purchases->pluck("tickets")->collapse();

        $data["id"] = $this->id;
        $data["event_id"] = (int) $this->event_id;
        $data["company_id"] = (int) $this->company_id;
        $data["parent_id"] = (int) $this->parent_id;
        $data["important"] = (int) $this->important;
        $data["lang"] = (string) $this->lang;
        $data["roles"] = $tickets->pluck("role")->unique();
        $data["tickets"] = ConnectTicketResource::collection($tickets );
        $data["code"] = (new Hashids())->encode( $this->id );


   
        return $data;

            
    }
}