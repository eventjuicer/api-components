<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;

class FieldResource extends Resource {


    public function toArray($request){
    
        $data = [];
        $data["id"] = $this->id;
        $data["name"] = $this->name;
        $data["format"] = $this->format;
        $data["min"] = $this->min;
        $data["max"] = $this->max;
        $data["options"] = $this->options;
        
        return $data;

    }
}