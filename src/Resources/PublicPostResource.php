<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\ValueObjects\CloudinaryImage;


class PublicPostResource extends Resource {


    protected $preventCriteriaOverwriting = false;

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
            "cover_image_id" => $this->cover_image_id,
            "is_published" => $this->is_published,
            "is_sticky"    => $this->is_sticky,
            "is_promoted"  => $this->is_promoted,
            "interactivity" => $this->interactivity,
            "created_at" => (string) $this->created_at,
            "updated_at" => (string) $this->updated_at,
            "published_at" => (string) $this->published_at,

            "category" => (string) $this->category,

            "company" => new PublicCompanyResource($this->company),
            "meta" => new PublicPostMetaResource($this->meta),
            "images" => PublicPostImageResource::collection($this->images),
            "cover" => $this->_cover,
            "og_image" => (string) (new CloudinaryImage($this->_cover))->wrapped("ehandel_cl_tmpl", "c_fill,w_1000", "x_-100,y_0")
        ];
    }
}
