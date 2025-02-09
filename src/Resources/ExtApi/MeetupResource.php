<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;

class MeetupResource extends Resource {

    public function toArray($request) {

        $data = [];
        $data["id"] = $this->id;
        $data["participant_id"] = $this->participant_id;
        return $data;
            
    }
}