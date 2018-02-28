<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

 
class PublicExhibitorResource extends Resource
{

    protected $presenterFields = ["cname2", "booth", "company_description", "logotype"];


    public function toArray($request)
    {

        $data = [];

		$data["company"] = new PublicCompanyResource($this->company);

		$data["fields"] = $this->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
        {     
            return [ $item->name => $item->pivot->field_value] ;

        })->all();

       	
 
        $data["roles"] = $this->purchases->filter(function($item){

        	return $item->status != "cancelled" ;

        })->pluck("tickets")->collapse()->pluck("role")->all();

      

		return $data;


            
    }
}



