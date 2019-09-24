<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;
use Eventjuicer\Services\Hashids;


class PublicContestant extends Resource
{

    protected $presenterFields = ["cname2", "product_name", "product_description", "logotype", "justification", "project_name"];


    public function toArray($request)
    {


        $data = $this->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
            {     
                return [ $item->name => $item->pivot->field_value ] ;

            })->all();


        $data["id"] = (int) $this->id;
        $data["ns"] = "participant";

        $data["logotype_cdn"] = "";

        $data["votes"] = $this->relationLoaded("votes") ? $this->votes->count() : 0;
   
        $data["tickets"] = $this->tickets->filter(function($item)
        {
         return $item->pivot->sold == 1;

        })->pluck("id");
        
        return $data;
    }
}



