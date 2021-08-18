<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 

class AdminAdminResource extends Resource
{


  
    public function toArray($request)
    {   


        $data = [

            "id" => $this->id,        
            
            "email" => $this->email,      

            "fname" => $this->fname,

            "lname" => $this->lname,  

            "phone" => $this->phone,  

            "position" => $this->position,  

            "profile" => $this->profile,  

            "roles" => $this->organization,

            "initials" => mb_strtoupper(
                mb_substr($this->fname, 0, 1) . 
                mb_substr($this->lname, 0, 1) 
            ),
            
            "access" => AdminAdminOrganizationsResource::collection($this->organizations),

           // "groups" => $this->organizations->pluck("groups")->collapse()->pluck("id")
        ];



        return $data;
    }
}



