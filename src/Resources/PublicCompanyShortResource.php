<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 
class PublicCompanyShortResource extends Resource {

    public function toArray($request){   

        $data = [

            "id" => $this->id,        
            
            "slug" => $this->slug,

            "featured" => $this->featured,

            "promo" => $this->promo,
            
        ];
        
        return $data;
    }

}



