<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PublicPostMetaResource extends Resource
{


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
    public function toArray($request)
    {
       return [

        
            "headline" => (string) $this->headline,
            "quote" => (string) $this->quote,    
            "body" => self::$includeBody ? (string) $this->body : ":)",
            "guestauthor" => self::$includeBody ? (string) $this->guestauthor : "",
            "metatitle" => self::$includeBody ? (string) $this->metatitle: "",
            "metadescription" => self::$includeBody ? (string) $this->metadescription: "",
            "keywords" => (string) $this->keywords,


        ];
    }
}
