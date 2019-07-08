<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;


class PartnerResource extends Resource
{

    static $groups;


    public static function setGroups($groups){

        self::$groups = $groups;

    }


    public function toArray($request)
    {

           return [

                "id"            => $this->id,
                "kind"          => $this->kind,
                "name"          => $this->name,
                "description"   => $this->description,
                "link"          => $this->link,
                "avatar"        => $this->avatar,
                "scopes(deprecated)" => self::$groups->map(function($item, $key){

                    if(in_array($this->id, $item)){
                        return $key;
                    }
                    return false;
                })->filter()->values()

           ];
    }
}



