<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

//use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;

use Eventjuicer\Services\Hashids;


class AdminFullParticipantResource extends Resource
{

    //protected $presenterFields = ["fname", "lname", "cname2", "position"];


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

        
        $data["purchases"] = AdminPurchaseResource::collection($this->whenLoaded('purchases'));


     //   $data["roles"] = $this->purchases->transform();


        // $data["tickets"] = $this->tickets->filter(function($item)
        // {
        //  return 1==1;//$item->pivot->sold ==1;

        // })->mapWithKeys(function($item)
        // {
        //  return [$item->id];
        // });



        // $data["roles"] = $paid_tickets->mapWithKeys(function($item)
        // {
        //  return [$item->role];
        // });

        // $data["tickets"] = $this->tickets->filter(function($item)
        // {
        //  return $item->pivot->sold == 0;

        // })->mapWithKeys(function($item){
        //  return [$item->id];
        // });

        $data["code"] = (new Hashids())->encode( $this->id );

        return $data;


            
    }
}



