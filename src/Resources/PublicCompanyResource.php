<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 

class PublicCompanyResource extends Resource
{

    protected $presenterFields = [

        "about", 
        "products",
        "expo", 
        "keywords",
        "website",
        "facebook",
        "twitter",
        "linkedin",
        "logotype",

    ];

    public function toArray($request)
    {   
        
 	
        return [

            "id" => $this->id,        
            "name" => $this->name ?? $this->slug,
            "slug" => $this->slug,
          	 
            "profile"   =>  $this->data->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
            {     

    
                return [ $item->name => $item->data ] ;

            })->all(),


          	"instances"=> $this->participants->pluck("ticketpivot")->collapse()->values()
        ];
    }
}



