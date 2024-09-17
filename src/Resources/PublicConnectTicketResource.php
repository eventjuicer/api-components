<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PublicConnectTicketResource extends Resource {


    public function toArray($request){
    
        $data = [];

        $data["id"] = $this->id;
        $data["role"] = $this->role;


   
        return $data;

            
    }
}