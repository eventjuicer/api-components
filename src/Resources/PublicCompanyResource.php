<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 

class PublicCompanyResource extends Resource
{

    protected $presenterFields = [

        "name",
        "about", 
        "products",
        "expo", 
        "keywords",
        "website",
        "facebook",
        "twitter",
        "linkedin",
        "logotype",
        "countries"

    ];

    public function toArray($request)
    {   
        
 	
        return [

            "id" => $this->id,        
            
            "slug" => $this->slug,
          	 
            "profile"   =>  $this->data->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
            {     

    
                return [ $item->name => $item->value ] ;

            })->all(),


          	"instances"=> $this->participants->pluck("ticketpivot")->collapse()->values()
        ];
    }
}



