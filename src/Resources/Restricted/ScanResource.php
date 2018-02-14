<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;



class ScanResource extends Resource
{

    protected $presenterFields = [
        "email", 
        "fname", 
        "lname", 
        "cname2", 
        "position", 
        "phone"
    ];


    public function toArray($request)
    {

        $data = [];

		$data["profile"] = $this->participant->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
        {     
            return [ $item->name => $item->pivot->field_value] ;

        })->all();

        $data["id"] = $this->id;
       
		$data["comments"] = ScanCommentResource::collection($this->comments);

        $data["created_at"] = (string) $this->created_at;

		return $data;


            
    }
}



