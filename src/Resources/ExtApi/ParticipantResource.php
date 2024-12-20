<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Hashids;


class ParticipantResource extends Resource
{

    public function toArray($request)
    {

        $data = parent::toArray($request);

        $data["fields"] = $this->fields->mapWithKeys(function($item)
        {     
            return [ $item->name => $item->pivot->field_value] ;

        })->all();

        
        $data["roles"] = $this->purchases->filter(function($item){

            return $item->status != "cancelled" ;

        })->pluck("tickets")->collapse()->pluck("role")->all();

    
        $data["code"] = (new Hashids())->encode( $this->id );

        return $data;

        
    }
}



