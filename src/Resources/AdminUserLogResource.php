<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 

class AdminUserLogResource extends Resource{


  
    public function toArray($request)
    {   


        $data = [

            "id" => $this->id,        
            
           
        ];



        return $data;
    }
}



