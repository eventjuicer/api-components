<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Hashids;

class SlaveParticipantResource extends Resource
{

    public function toArray($request){
        
        $data = parent::toArray($request);

        $data["fields"] = $this->fields->mapWithKeys(function($item)
        {     
            return [ $item->name => $item->pivot->field_value] ;
        })->all();

        $data["important"] = intval($this->important || !empty($data["fields"]["important"]) );
        $data["code"] = (new Hashids())->encode( $this->id );

        return $data;

       
    }
    
}