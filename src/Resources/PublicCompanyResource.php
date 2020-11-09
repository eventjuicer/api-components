<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 
use Eventjuicer\Services\Cloudinary; 

use Eventjuicer\ValueObjects\CloudinaryImage;


class PublicCompanyResource extends Resource
{


    public static $skipPurchases = false;
    public static $skipProfile = false;
    public static $skipLongTexts = false;


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

    public static function disableLongTexts()
    {
        self::$skipLongTexts = true;
    }

    public static function enableLongTexts()
    {
        self::$skipLongTexts = false;
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


        $profile = array_merge(array_flip(self::$presenterFields), $this->data->whereIn("name", self::$presenterFields)->mapWithKeys(function($item){     

                    return [ $item->name => $item->value ] ;

        })->all());


        if(self::$skipLongTexts){
            unset($profile["about"]);
            unset($profile["expo"]);
            unset($profile["products"]);
        }

        $logotype_thumbnail = (new CloudinaryImage($profile["logotype_cdn"]))->thumb(600, 600);
        //we take opengraph_image_cdn and resize it if needed...
        $og_image = (new CloudinaryImage($profile["opengraph_image_cdn"]))->thumb(1200, 630);

        //it should be taken from settings....
        $profile["og_template"] = $this->group_id > 1 ? 'ebe5_template' : 'template_teh19_exhibitor';
        $profile["thumbnail"] = $logotype_thumbnail ?? $profile["logotype"];

        $lang = !empty($profile["lang"]) ? $profile["lang"] : $this->group_id > 1 ? "en" : "pl";

        $profile["og_image"] = $og_image ?? (new CloudinaryImage($profile["logotype_cdn"]))->wrapped($profile["og_template"] . "_" . $lang);

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



