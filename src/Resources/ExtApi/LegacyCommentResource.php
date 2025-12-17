<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;

class LegacyCommentResource extends Resource {


    public function toArray($request){
    
        $data = [];
        $data["id"] = $this->id;
        $data["organizer_id"] = (int) $this->organizer_id;
        $data["event_id"] = (int) $this->event_id;
        $data["participant_id"] = (int) $this->object_id;

        $data["content"] = $this->comment;
        $data["created_at"] = (string)$this->createdon;

        $data["legacy_admin_id"] = (int) $this->admin_id;
 
        return $data;

    }
}