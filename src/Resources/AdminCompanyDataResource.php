<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AdminCompanyDataResource extends Resource
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
            $data["company_id"] = $this->company_id;
            $data["name"] = $this->name;
            $data["value"] = $this->value;
            $data["created_at"] = (string) $this->created_at;
            $data["updated_at"] = (string) $this->updated_at;

            return $data;

        
    }


}



