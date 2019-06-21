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
            "quote" => (string) $this->quote_parsed,
            "body" => self::$includeBody ? (string) $this->body_parsed : "",

            "guestauthor" => (string) $this->guestauthor,
            "metatitle" => (string) $this->metatitle,
            "metadescription" => (string) $this->metadescription,


        ];
    }
}
