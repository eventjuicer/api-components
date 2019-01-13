<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 


class AdminCompanyResource extends Resource
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

        $purchases = $this->participants->pluck("ticketpivot")->collapse()->where("sold", 1);


        $data = [

            "id" => $this->id,        
            
            "slug" => $this->slug,

            "featured" => $this->featured,


            "debut" => $this->debut, 

            "promo" =>   $this->promo,
 
            "points" =>   $this->points,
            
            "position" =>   $this->position,

            "admin_id" => $this->admin_id,

            //"admin"  => new AdminAdminResource( $this->admin),

            "profile"   =>  $this->data->whereIn("access", "company")->mapWithKeys(function($item)
            {     
                return [ $item->name => $item->value ] ;

            })->all(),


            "settings"   =>  $this->data->whereIn("access", "admin")->mapWithKeys(function($item)
            {     
                return [ $item->name => $item->value ] ;

            })->all(),

            "participant_ids" => $this->participants->pluck("id"),

            "purchase_ids" =>  $purchases->pluck("purchase_id"),

            "event_ids" => $purchases->pluck("event_id")->unique()->values(),

            "ticket_ids" => $purchases->pluck("ticket_id")->unique()->values(),

          	"instances" => $this->when(
                !self::$skipPurchases, 
                $this->participants->pluck("ticketpivot")->collapse()->values()
            )
            
        ];

        


        return $data;
    }
}



