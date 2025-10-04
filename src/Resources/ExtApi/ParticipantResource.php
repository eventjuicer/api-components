<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Hashids;




class ParticipantResource extends Resource
{

    public static $companyFields = ["name", "keywords", "logotype_cdn", "lang", "website", "facebook", "twitter", "linkedin", "xing"];


    public function toArray($request)
    {

        $notCancelledPurchases = $this->purchases->filter(function($item){
            return $item->status != "cancelled" ;
        });

        $notCancelledTickets = $notCancelledPurchases->pluck("tickets")->collapse();

        $data = parent::toArray($request);

        $data["fields"] = $this->fields->mapWithKeys(function($item)
        {     
            return [ $item->name => $item->pivot->field_value] ;
        })->all();

        $data["fields"]["company"] =  $this->company? $this->company->data->whereIn("name", static::$companyFields)->mapWithKeys(function($item){
            return [ $item->name => $item->value ] ;
        }): [];

        if($this->company && $this->company->id){
            $data["fields"]["company"]["promoted"] = (int)$this->company->promo;
            $data["fields"]["company"]["featured"] = (int) $this->company->featured;
            $data["fields"]["company"]["premium"] = (int)$this->company->premium;
        }


        $data["roles"] = $notCancelledTickets->pluck("role")->unique()->values()->all();
        $data["ticket_ids"] = $notCancelledTickets->pluck("id")->unique()->values()->all();
        $data["purchase_ids"] = $this->purchases->pluck("id")->unique()->values()->all();

        $data["important"] = intval($this->important || !empty($data["fields"]["important"]) );
        $data["code"] = (new Hashids())->encode( $this->id );

        return $data;

       
    }
    
}