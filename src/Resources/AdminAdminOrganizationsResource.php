<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 

class AdminAdminOrganizationsResource extends Resource
{


  
    public function toArray($request)
    {   


        $data = $this->pivot;

        $data["groups"] = $this->groups->pluck("id");


        return $data;
    }
}



