<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 

class AdminParticipantFieldResource extends Resource
{

    public function toArray($request)
    {   


        return [

            "id" => $this->id,
            "name" => $this->input->name,
            "value" => $this->field_value,
            "updated_at" => $this->updatedon
            
    
        ];

            
    }
}



