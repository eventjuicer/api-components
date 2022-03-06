<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;

class CompanyDataResource extends Resource
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

            $data["id"]     = $this->id;
            $data["name"]   = $this->name;
            $data["value"]  = $this->value;
            $data["summary"]  = is_string($this->value) ? mb_substr(strip_tags($this->value), 0, 50) : $this->value;
            $data["created_at"] = (string) $this->created_at;
            $data["updated_at"] = (string) $this->updated_at;

            return $data;

    }


    protected function remapFields($fields)
    {
           return $fields->whereIn("name", $this->visible)->mapWithKeys(function($item)
            {     

                $value = $item->pivot->field_value;

                return [ $item->name => $value ] ;

            })->all();
    }
}



