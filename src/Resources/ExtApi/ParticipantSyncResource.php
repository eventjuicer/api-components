<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;


class ParticipantSyncResource extends Resource
{

    public function toArray($request)
    {

        $data = [];
        $data["id"] = (int) $this->id;
        $data["updated_at"] = (string) $this->createdon;

        return $data;

        
    }
}



