<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;


class ScanCommentResource extends Resource
{

    public function toArray($request)
    {

        $data = [];		
        $data["id"] = $this->id;
        $data["comment"] = $this->comment;
		return $data;
        
    }
    
}



