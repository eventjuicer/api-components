<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class CreativeResource extends Resource
{

    protected static $dataDefaults = [

        "sender_name"   => "",
        "sender_email"  => "",
        "template"      => ""

    ];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

      
       return [
            "id"    => (int) $this->id,

            "name" => $this->name,
            "act_as" => $this->act_as,
            "created_at" => (string) $this->created_at,
            "updated_at" => (string) $this->updated_at,
            "data" => array_merge(
                            self::$dataDefaults, 
                            (array) $this->data
                       )
        ];
    }

  
  
 

}
