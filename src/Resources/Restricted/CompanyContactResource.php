<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;



class CompanyContactResource extends Resource
{

    protected $profileFields = [

        "fname"     => "", 
        "lname"     => "", 
        "cname2"    => "", 
        "phone"     => "", 
        "position"  => ""
    ];


    public function toArray($request)
    {

        $data = [];

        $data["id"] = $this->id;
        $data["email"] = $this->email;
       
        $data["contactlists"] =  CompanyContactlistResource::collection($this->contactlists);
        
        $data["contactlist_ids"] = $this->contactlists->pluck("id");

        $data["starred"]    =  (int) $this->starred;
        $data["muted"]      =  (int) $this->muted;

        $data["data"]     = array_merge($this->profileFields, 
            (array) $this->data);

        $data["comment"] = (string) $this->comment;
       
        $data["created_at"] = (string) $this->created_at;
        $data["updated_at"] = (string) $this->updated_at;
        $data["sent_at"] = (string) $this->sent_at;


        return $data;
 


            
    }
}



