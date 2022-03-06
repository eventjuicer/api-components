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

            $value = is_string($this->value)? strip_tags($this->value): $this->value;

            $data = [];

            $data["id"]     = $this->id;
            $data["name"]   = $this->name;
            $data["value"]  = $value;
            $data["summary"]  = is_string($value)? mb_substr($value, 0, 50) : $value;
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



