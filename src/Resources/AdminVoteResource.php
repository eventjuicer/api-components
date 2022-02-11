<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AdminVoteResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = [];
      
        
        $data["id"] = $this->id;
        $data["created_at"] = (string) $this->created_at;
        
        $data["account"] = new AdminSocialLinkedinResource($this->voteable);
        $data["contestant"] = new AdminVotedParticipantResource($this->participant);

        return $data;
    }
}




