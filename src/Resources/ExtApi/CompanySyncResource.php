<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;


class CompanySyncResource extends Resource
{

    public function toArray($request)
    {

        $data = [];
        $data["id"] = (int) $this->id;
        $data["organizer_id"] = (int) $this->organizer_id;
        $data["group_id"] = (int) $this->group_id;

        return $data;

        
    }
}



