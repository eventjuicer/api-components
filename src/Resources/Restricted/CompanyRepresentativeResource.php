<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;


use Eventjuicer\Services\Personalizer;


class CompanyRepresentativeResource extends Resource
{


    protected $skipParentCompany;

    public function __construct($resource, $skipParentCompany = false)
    {
        $this->resource = $resource;
        $this->skipParentCompany = $skipParentCompany;
    }


    public function toArray($request)
    {       


          $data = [];

          $data["id"] = $this->id;

          $data["profile"] = ( new Personalizer($this->resource) )->getProfile();

          $data["created_at"] = (string) $this->createdon;

          $data["updated_at"] = (string) $data["created_at"];

          return $data;

    }


    // protected function remapFields($fields)
    // {
    //        return $fields->whereIn("name", $this->visible)->mapWithKeys(function($item)
    //         {     

    //             $value = $item->pivot->field_value;

    //             return [ $item->name => $value ] ;

    //         })->all();
    // }
}



