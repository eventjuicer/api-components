<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Resources\Admin\ReportTicketResource;
use Eventjuicer\ValueObjects\CloudinaryImage;

class MobileAppCompanyResource extends Resource
{

    protected static $eventId = 0;

    protected $presenterFields = [

        "name",
        "logotype_cdn",
        "lang"

    ];

    public static function setEventId($eventId){
        self::$eventId = $eventId;
    }

    public function toArray($request)
    {   

        $profile = $this->data->whereIn("name", $this->presenterFields)->mapWithKeys(function($item){     

                return [ $item->name => $item->value ] ;

        })->all();
        
        $profile["logotype_cdn"] = !empty($profile["logotype_cdn"]) ? (new CloudinaryImage($profile["logotype_cdn"]))->thumb() : "";

        $profile["name"] = !empty($profile["name"]) ? $profile["name"] : $this->slug;

        $profile["lang"] = !empty($profile["lang"]) ? $profile["lang"] : "en";

        return [

            "id" => $this->id,
            
            "reps" => CompanyRepresentativeResource::collection($this->reps),

            "purchases" => ReportTicketResource::collection($this->purchases),

            "profile"   =>  $profile,

        ];
    }
}



