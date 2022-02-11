<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AdminVotedParticipantResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
       
        $profile = [];
        
        $profile["id"] = $this->id;
        $profile["email"] = $this->email;
    
        return $profile;
    }
}




