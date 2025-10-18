<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Hashids;

class SlaveParticipantResource extends Resource
{

    public function toArray($request){
        
        $data = [];

        $data["id"] = $this->id;
        $data["email"] = $this->email;
        $data["token"] = $this->token;
        $data["parent_id"] = $this->parent_id;

        $data["important"] = intval($this->important || !empty($data["fields"]["important"]) );
        
        $data["code"] = (new Hashids())->encode( $this->id );

        $data["created_at"] = $this->createdon;

        $data["fields"] = $this->fields->mapWithKeys(function($item)
        {     
            return [ $item->name => $item->pivot->field_value] ;
        })->all();


        return $data;

       
    }
    
}