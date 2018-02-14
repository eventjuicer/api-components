<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;


use Eventjuicer\Services\Hashids;


class ScannerResource extends Resource
{

    protected $presenterFields = ["fname", "lname", "cname2"];


    public function toArray($request)
    {

		$data = $this->fields->whereIn("name", $this->presenterFields)->mapWithKeys(function($item)
        {     
            return [ $item->name => $item->pivot->field_value] ;

        })->all();

       
		$data["code"] = (new Hashids())->encode( $this->id );

		return $data;


            
    }
}



