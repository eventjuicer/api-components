<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 

class PublicCompanyResource extends Resource
{

    public function toArray($request)
    {   
        
 	 

        return [

            "id" => $this->id,        
            "name" => $this->name ?? $this->slug,
            "slug" => $this->slug,
          	
          	"tickets"=> $this->participants->pluck("ticketpivot")->collapse()
        ];
    }
}



