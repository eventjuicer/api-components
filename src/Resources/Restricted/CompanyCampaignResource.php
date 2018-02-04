<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;



class CompanyCampaignResource extends Resource
{



    public function toArray($request)
    {

        $data = [];

        $data["id"] = $this->id;
        $data["name"] = $this->name;
       
        $data["created_at"] = (string) $this->created_at;
        $data["updated_at"] = (string) $this->updated_at;

        $data["admin"] = new ApiUserResource($this->admin, true);

        return $data;
 


            
    }
}



