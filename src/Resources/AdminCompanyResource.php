<?php

namespace Eventjuicer\Resources;
use Illuminate\Http\Resources\Json\Resource; 
use Eventjuicer\ValueObjects\CloudinaryImage;

class AdminCompanyResource extends Resource {


    public static $skipPurchases = false;

    public static function disablePurchases(){
        self::$skipPurchases = true;
    }


    public function toArray($request){   


        $purchases = $this->participants->pluck("ticketpivot")->collapse()->where("sold", 1);

        $profile = $this->data->mapWithKeys(function($item){     
                return [ $item->name => $item->value ];
        })->all();

        //it should be taken from settings....
        $profile["og_template"] = $this->group_id > 1 ? 'ebe5_2__template' : 'template_teh21_exhibitor';
        $lang = !empty($profile["lang"]) ? $profile["lang"] : $this->group_id > 1 ? "en" : "pl";


        if(!empty($profile["logotype_cdn"])){
            $logotype_thumbnail = (new CloudinaryImage($profile["logotype_cdn"]))->thumb(600, 600);

            $logotype_wrapped =  (new CloudinaryImage($profile["logotype_cdn"]))->wrapped($profile["og_template"] . "_" . $lang);

        }

        if(!empty($profile["opengraph_image_cdn"])){

            //we take opengraph_image_cdn and resize it if needed...
            $og_image = (new CloudinaryImage($profile["opengraph_image_cdn"]))->thumb(1200, 630);   

        }


        $profile["og_image"] = $og_image ?? $logotype_wrapped ?? null;
    
        
        $profile["thumbnail"] = $logotype_thumbnail ?? $profile["logotype"] ?? null;


       

        
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

            "profile" => $profile,

            // "settings"   =>  $this->data->whereIn("access", "admin")->mapWithKeys(function($item)
            // {     
            //     return [ $item->name => $item->value ] ;

            // })->all(),

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



