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
          	 
            "fields" => [

                        "company_description" => "lorem ipsum",
                        "logotype" => "lorem ipsum", 
                        "cname2" => "lorem ipsum"
                      ],

          	"instances"=> $this->participants->pluck("ticketpivot")->collapse()
        ];
    }
}



