<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Hashids;


class ExtAuthResource extends Resource
{

    protected $exhibitorFields = ["cname2", "booth", "logotype", "company_website"];

    public function toArray($request)
    {

        $data = $this->fields->whereIn("name", $this->exhibitorFields)->mapWithKeys(function($item)
            {
                
                return [ $item->name => $item->pivot->field_value ];

            })->all();

        $data["participant_id"] = $this->id;
        
        $data["jwt"] = $this->jwt;

        return $data;
            
    }
}



