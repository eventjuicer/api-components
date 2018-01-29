<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;



class CompanyImportResource extends Resource
{

    protected $skipDependencies;

    public function __construct($resource, $skipDependencies = false)
    {
        $this->resource = $resource;

        $this->skipDependencies = $skipDependencies;
    }


    public function toArray($request)
    {

        $data = [];

        $data["id"] = $this->id;
        $data["name"] = $this->name;


        if(!$this->skipDependencies)
        {
            $data["contactlist"] = new CompanyContactlistResource($this->contactlist);
            $data["admin"] = new ApiUserResource($this->admin, true);
        }
        

        $data["submitted"] =  (int) $this->submitted;
        $data["imported"] =  (int) $this->imported;



        $data["imported_at"] = (string) $this->imported_at;
        $data["created_at"] = (string) $this->created_at;
        $data["updated_at"] = (string) $this->updated_at;

        

        return $data;
 


            
    }
}



