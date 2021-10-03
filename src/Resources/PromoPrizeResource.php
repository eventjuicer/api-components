<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PromoPrizeResource extends Resource {

    public function toArray($request) {
    
        $data = [];

        $data["id"] = $this->id;
        $data["organizer_id"] = $this->organizer_id;
        $data["group_id"] = $this->group_id;

        $data["name"] = $this->name;
        $data["disabled"] = $this->disabled;

        $data["min"] = $this->min;
        $data["max"] = $this->max;
        $data["level"] = $this->level;

        $data["created_at"] = (string) $this->created_at;
        $data["updated_at"] = (string) $this->updated_at;

        return $data;
            
    }
}