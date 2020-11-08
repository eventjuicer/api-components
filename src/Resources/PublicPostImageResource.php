<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PublicPostImageResource extends Resource {

    public function toArray($request){
       return [

            "id"        => (int) $this->id,
            "path"      => $this->path,
            "is_cover"      => $this->is_cover

        ];
    }
}
