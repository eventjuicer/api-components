<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;

class MeetupResource extends Resource {


    public function toArray($request) {

        $data = [];
        $data["id"] = $this->id;
        $data["participant_id"] = $this->participant_id;
        $data["rel_participant_id"] = $this->rel_participant_id;
        $data["agreed"] =  (int) $this->agreed;
        $data["responded_at"] = (string) $this->responded_at;
        $data["created_at"] = (string) $this->created_at;

        return $data;
            
    }
}