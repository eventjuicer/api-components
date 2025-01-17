<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 
use Eventjuicer\Services\Cloudinary; 

use Eventjuicer\ValueObjects\CloudinaryImage;


class PublicCompanyLeanResource extends Resource
{

    protected static $presenterFields = [

        "name",
        "about", 
        "products",
        "keywords",        
        "logotype_cdn",
        "logotype"

    ];    
    public function toArray($request){   


        $profile = array();
        
        
        $profile = array_merge(array_flip(self::$presenterFields), $this->data->whereIn("name", self::$presenterFields)->mapWithKeys(function($item) {     

                    return [$item->name =>  (int)$this->group_id === 1 & is_string($item->value)? strip_tags($item->value): $item->value];

        })->all());


        $profile["about"] = mb_substr($profile["about"], 0, 500);
        $profile["products"] = mb_substr($profile["products"], 0, 500);
 
        $logotype_thumbnail = (new CloudinaryImage($profile["logotype_cdn"]))->thumb(600, 600);
 
        $profile["thumbnail"] = $logotype_thumbnail ?? $profile["logotype"];
        unset($profile["logotype"]);
 
    
        $data = [

            "id" => $this->id,        

            "organizer_id" => $this->organizer_id,        

            "group_id" => $this->group_id,        
            
            "slug" => $this->slug,

            "featured" => $this->featured,

            "debut" => $this->debut,

            "promo" => $this->promo,

            "profile"   =>  $profile,

            
        ];
        
        return $data;
    }


}



