<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PlannerCompanyResource extends Resource
{


    protected static $presenterFields = [

        "name",
        "about", 
        "lang",
        "keywords",
        "website",
        "facebook",
        "twitter",
        "linkedin",
        "countries",
        "logotype_cdn",
    ];

     
   
    public function toArray($request){   

        $profile = array_merge(array_flip(self::$presenterFields), $this->data->whereIn("name", self::$presenterFields)->mapWithKeys(function($item) {     

                    return [$item->name =>  $item->value];

        })->all());


        $data = [

            "id" => $this->id,        
            "organizer_id" => $this->organizer_id,        
            "group_id" => $this->group_id,        
            "slug" => $this->slug,
            "featured" => $this->featured,
            "debut" => $this->debut,
            "promo" => $this->promo,
            "profile"   =>  $profile,
            "booths" => $this->participants->pluck("ticketpivot")->collapse()->pluck("formdata")->filter()->values(),
        ];

        return $data;
    }


}



