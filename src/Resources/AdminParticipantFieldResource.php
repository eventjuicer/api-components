<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 

class AdminParticipantFieldResource extends Resource
{

    public function toArray($request)
    {   


        return [

            "id" => $this->id,
            "event_id" => $this->event_id,
            "group_id" => $this->group_id,
            "participant_id" => $this->participant_id,
            "name" => $this->input->name,
            "value" => $this->field_value,
            "updated_at" => (string) $this->updatedon
            
    
        ];

            
    }
}



