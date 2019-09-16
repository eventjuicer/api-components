<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;
use Eventjuicer\Services\Hashids;


class PresenterResource extends Resource
{

    protected $presenterFields = [

        "fname"     => 2, 
        "lname"     => 3, 
        "presenter" => 61,
        "cname2"    => 11, 
        "position"  => 24, 
        "presentation_title"        => 21, 
        "presentation_description"  => 58,
        "presentation_venue"        => 98,
        "presentation_time"         => 59,
        "avatar"                    => 14,
        "logotype"                  => 55,
        "avatar_cdn"                => 254, //14,
        "logotype_cdn"              => 255, //55,
        "bio"                       => 23,
        "featured"                  => 250,
        "custom_admin_1"            => 90
    ];


    public function toArray($request)
    {

        if( ! $this->relationLoaded("fieldpivot") ){
          
            throw new \Exception("Use fieldpivot");

        }

        // array_walk($data, function(&$v, $k){

        //     $v = $v*1000;
        // });


        $profile = $this->fieldpivot->mapWithKeys(function($item){

            $key = array_search($item->field_id, $this->presenterFields);

            if($key){
                return [$key => $item->field_value];
            }

            return [];
           })->all();

        $data = array_merge(array_fill_keys(array_keys($this->presenterFields), ""), $profile);


        $data["id"] = (int) $this->id;

        $data["event"] = new PublicEventResource($this->whenLoaded("event"));

        return $data;
    }
}



