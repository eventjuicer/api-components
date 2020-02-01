<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 
use Eventjuicer\Services\Cloudinary; 

use Eventjuicer\ValueObjects\CloudinaryImage;


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

    public static function setFields(array $fields){
        self::$presenterFields = $fields;
    }

    public function toArray($request)
    {   


        $profile = $this->data->whereIn("name", self::$presenterFields)->mapWithKeys(function($item){     

                    return [ $item->name => $item->value ] ;

        })->all();

        //it should be taken from settings....
        $profile["og_template"] = $this->group_id > 1 ? 'ebe5_template' : 'template_4';

        $profile["thumbnail"] = !empty($profile["logotype_cdn"]) ? (new CloudinaryImage($profile["logotype_cdn"]))->thumb() : '';

        $data = [

            "id" => $this->id,        
            
            "slug" => $this->slug,

            "featured" => $this->featured,

            "debut" => $this->debut,

            "promo" => $this->promo,

            "profile"   =>  $this->when(!self::$skipProfile, $profile),

            "instances" =>  !self::$skipPurchases && $this->hasTicketPivot() ? 
                $this->participants->pluck("ticketpivot")->collapse()->values() : []
            
        ];

        
        return $data;
    }


    protected function hasTicketPivot(){

        $p = $this->relationLoaded("participants");

        if($p && $this->participants->first() ){
            return $this->participants->first()->relationLoaded("ticketpivot");
        }

        return false;
    }
}



