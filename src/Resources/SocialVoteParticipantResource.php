<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;



class SocialVoteParticipantResource extends Resource
{

    protected $presenterFields = [

        "presenter"                 => 61,
        "cname2"                    => 11, 
        "position"                  => 24, 
        "presentation_title"        => 21, 
        "presentation_description"  => 58,
        "avatar"                    => 14,
        "logotype"                  => 55,
        "avatar_cdn"                => 254, 
        "logotype_cdn"              => 255, 
        "featured"                  => 250,
        "custom_admin_1"            => 90
    ];

    public function toArray($request)
    {

        $data = array();

        if( ! $this->relationLoaded("fieldpivot") ){
          
            throw new \Exception("Use fieldpivot");

        }


		 $profile = $this->fieldpivot->mapWithKeys(function($item){

            $key = array_search($item->field_id, $this->presenterFields);

            if($key){
                return [$key => $item->field_value];
            }

            return [];
           })->all();

        $data = array_merge(array_fill_keys(array_keys($this->presenterFields), ""), $profile);


        $data["id"] = (int) $this->id;
        $data["participant_id"] = (int) $this->id;

        return $data;


            
    }
}



