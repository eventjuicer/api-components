<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;

 


class CompanyPerformanceResource extends Resource
{

    protected $visible = ["name", "website", "logotype"];
  
    public function toArray($request)
    {       

            if(empty($this->id))
            {
                return [];
            }
            
            $data = [];

            $data["id"] = $this->id;

            $data["slug"] = $this->slug;

            $data["data"] = array_merge(

                array_fill_keys($this->visible, ""), $this->remapFields($this->data)
            );

            $data["stats"] = $this->stats;

            return $data;
    }


    protected function remapFields($fields)
    {
           return $fields->whereIn("name", $this->visible)->mapWithKeys(function($item)
            {     

                return [ $item->name => $item->value ] ;

            })->all();
    }
}



