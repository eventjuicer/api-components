<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 
use Eventjuicer\Services\Cloudinary; 


class PublicCompanyResource extends Resource
{


    public static $skipPurchases = false;
    public static $skipProfile = false;


    protected static $presenterFields = [

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

    public static function enablePurchases()
    {
        self::$skipPurchases = false;
    }

      public static function disableProfile()
    {
        self::$skipProfile = true;
    }

    public static function enableProfile()
    {
        self::$skipProfile = false;
    }


    public function toArray($request)
    {   

        $data = [

            "id" => $this->id,        
            
            "slug" => $this->slug,

            "featured" => $this->featured,

            "debut" => $this->debut,

            "promo" => $this->promo,

            "profile"   =>  $this->when(!self::$skipProfile, 

                $this->data->whereIn("name", self::$presenterFields)->mapWithKeys(function($item)
                {     

                    return [ $item->name => $item->value ] ;

                })->all()
            ),

          	"instances" => $this->when( !self::$skipPurchases, 
                $this->participants->pluck("ticketpivot")->collapse()->values()
            )
            
        ];

        


        return $data;
    }
}



