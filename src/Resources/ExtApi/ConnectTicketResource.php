<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;

class ConnectTicketResource extends Resource {


    public function toArray($request){
    
        $data = [];
        $data["id"] = $this->id;
        $data["role"] = $this->role;

        return $data;

    }
}