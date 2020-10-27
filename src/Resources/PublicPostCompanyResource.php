<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 
use Eventjuicer\Services\Cloudinary; 

use Eventjuicer\ValueObjects\CloudinaryImage;


class PublicPostCompanyResource extends Resource
{

 
    protected static $presenterFields = [

        "name",
        "lang",
        "keywords",
        "website",
        "facebook",
        "twitter",
        "linkedin",
        "countries",
        "logotype_cdn",
        "opengraph_image_cdn"
    ];




    public function toArray($request)
    {   
     
        $data =  $this->data->whereIn("name", self::$presenterFields)->mapWithKeys(function($item){     
                    return [ $item->name => $item->value ];
            })->all();

        $data["id"] = $this->id;
        $data["slug"] = $this->slug;
        $data["featured"] = $this->featured;

        return $data;
    }

}



