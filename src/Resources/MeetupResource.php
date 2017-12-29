<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

//use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;

use Eventjuicer\Services\Hashids;

class MeetupResource extends Resource
{

    //protected $presenterFields = ["fname", "lname", "cname2", "position"];


    public function toArray($request)
    {

		$data = parent::toArray($request);

        $data["participant"] = new PublicVisitor($this->participant);

        $data["admin"] = new PublicVisitor($this->admin);


		// $data["participant"]["fields"] = $this->participant->fields->mapWithKeys(function($item)
  //       {     
  //           return [ $item->name => $item->pivot->field_value] ;

  //       })->all();

        // $data["tickets"] = TicketResource::collection($this->whenLoaded("purchases.tickets"));
      	
 
      //   $data["roles"] = $this->purchases->filter(function($item){

      //   	return $item->status != "cancelled" ;

      //   })->pluck("tickets")->collapse()->pluck("role")->all();

     	
     	// $data["purchases"] = PurchaseResource::collection($this->whenLoaded('purchases'));


     //   $data["roles"] = $this->purchases->transform();


        // $data["tickets"] = $this->tickets->filter(function($item)
        // {
        // 	return 1==1;//$item->pivot->sold ==1;

        // })->mapWithKeys(function($item)
        // {
        // 	return [$item->id];
        // });



        // $data["roles"] = $paid_tickets->mapWithKeys(function($item)
        // {
        // 	return [$item->role];
        // });

        // $data["tickets"] = $this->tickets->filter(function($item)
        // {
        // 	return $item->pivot->sold == 0;

        // })->mapWithKeys(function($item){
        // 	return [$item->id];
        // });

		//$data["code"] = (new Hashids())->encode( $this->id );

		return $data;


            
    }
}



