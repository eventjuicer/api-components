<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;



class CompanyImportResource extends Resource
{


    public function toArray($request)
    {

        $data = [];

        $data["id"] = $this->id;
        $data["name"] = $this->name;
        $data["count"] =  (int) $this->count;
      
        $data["imported_at"] = (string) $this->imported_at;
        $data["created_at"] = (string) $this->created_at;
        $data["updated_at"] = (string) $this->updated_at;

        $data["admin"] = new ApiUserResource($this->admin, true);

        return $data;
 


            
    }
}



