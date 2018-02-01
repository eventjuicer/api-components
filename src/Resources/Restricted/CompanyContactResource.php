<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;



class CompanyContactResource extends Resource
{


    public function toArray($request)
    {

        $data = [];

        $data["id"] = $this->id;
        $data["email"] = $this->email;
       
        $data["contactlists"] =  CompanyContactlistResource::collection($this->contactlists);

        $data["starred"]    =  (int) $this->starred;
        $data["muted"]      =  (int) $this->muted;
        $data["fields"]     = $this->fields;

       
        $data["created_at"] = (string) $this->created_at;
        $data["updated_at"] = (string) $this->updated_at;
        $data["sent_at"] = (string) $this->sent_at;


        return $data;
 


            
    }
}



