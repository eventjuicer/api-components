<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 

class PublicCompanyResource extends Resource
{


    public static $skipPurchases = false;


    protected $presenterFields = [

        "name",
        "about", 
        "products",
        "lang",
        "expo", 
        "keywords",
        "website",
        "facebook",
        "twitter",
        "linkedin",
        "logotype",
        "opengraph_image",
        "countries",
        
        "logotype_cdn",
        "opengraph_image_cdn"

    ];

    public static function disablePurchases()
    {
        self::$skipPurchases = true;
    }


    public function toArray($request)
    {   

        return [

            "id" => $this->id,        
            
            "slug" => $this->slug,

            "featured" => $this->featured,

            "debut" => $this->debut,
          	 
            "profile"   =>  $this->data->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
            {     

                return [ $item->name => $item->value ] ;

            })->all(),


          	"instances" => $this->when(
                !self::$skipPurchases, 
                $this->participants->pluck("ticketpivot")->collapse()->values()
            )
            
        ];
    }
}



