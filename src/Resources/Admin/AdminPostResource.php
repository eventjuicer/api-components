<?php

namespace Eventjuicer\Resources\Admin;

use Illuminate\Http\Resources\Json\Resource;

use Eventjuicer\Resources\PublicCompanyResource;
use Eventjuicer\Resources\PublicPostMetaResource;
use Eventjuicer\Resources\PublicPostImageResource;

class AdminPostResource extends Resource {


    static $includeBody = false;

    static function includeBody($boolval){
        self::$includeBody = $boolval;
    }


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request){

       if(self::$includeBody){
        PublicPostMetaResource::includeBody(true);
       }

       PublicCompanyResource::disablePurchases();
       PublicCompanyResource::enableProfile();
       PublicCompanyResource::disableLongTexts();


       return [

            "id"        => (int) $this->id,

            "admin_id"      => $this->admin_id,
            "editor_id"      => $this->editor_id,

            "is_published" => $this->is_published,
            "is_sticky"    => $this->is_sticky,
            "is_promoted"  => $this->is_promoted,
            "interactivity" => $this->interactivity,
            "created_at" => (string) $this->created_at,
            "updated_at" => (string) $this->updated_at,
            "published_at" => (string) $this->published_at,

            "company" => new PublicCompanyResource($this->company),
            "meta" => new PublicPostMetaResource($this->meta),
            "images" => PublicPostImageResource::collection($this->images),
            "cover" => $this->_cover

        ];
    }
}
