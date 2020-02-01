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
        "logotype_cdn"

    ];

    public static function setEventId($eventId){
        self::$eventId = $eventId;
    }

    public function toArray($request)
    {   
        

        return [

            "id" => $this->id,
      
            "name" => $this->name ?? $this->slug,

            "slug" => $this->slug,
        
            "assigned_at" => (string) $this->assigned_at,
            
            "has_password" => intval( strlen($this->password) === 40),

            "reps" => CompanyRepresentativeResource::collection($this->reps),

            "purchases" => ReportTicketResource::collection($this->purchases),

            "profile"   =>  $this->data->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
            {     

                return [ $item->name => $item->value ] ;

            })->all(),

        ];
    }
}



