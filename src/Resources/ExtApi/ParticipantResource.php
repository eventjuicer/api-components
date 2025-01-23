<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Hashids;




class ParticipantResource extends Resource
{

    public static $companyFields = ["name", "keywords", "logotype_cdn"];


    public function toArray($request)
    {

        $data = parent::toArray($request);

        $data["fields"] = $this->fields->mapWithKeys(function($item)
        {     
            return [ $item->name => $item->pivot->field_value] ;
        })->all();

        $data["fields"]["company"] =  $this->company? $this->company->data->whereIn("name", static::$companyFields)->mapWithKeys(function($item){
            return [ $item->name => $item->value ] ;
        }): [];

        if($this->company && $this->company->id){
            $data["fields"]["company"]["promo"] = $this->company->promo;
        }

        $data["roles"] = $this->purchases->filter(function($item){
            return $item->status != "cancelled" ;
        })->pluck("tickets")->collapse()->pluck("role")->all();

        $data["important"] = $this->important || !empty($data["fields"]["important"]);
    
        $data["code"] = (new Hashids())->encode( $this->id );

        return $data;

        
    }
}



