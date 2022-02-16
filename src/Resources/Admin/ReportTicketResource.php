<?php

namespace Eventjuicer\Resources\Admin;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Resources\PurchaseResource;


class ReportTicketResource extends Resource
{



    public function toArray($request)
    {       

        $data = [];
        
        $data["id"] = $this->id;
        $data["translation_asset_id"] = $this->translation_asset_id;
        $data["internal_name"] = $this->internal_name;
        $data["___name"] = array_get($this->names, "en");
        $data["quantity"] = $this->pivot->quantity;
        $data["formdata"] = is_array($this->pivot->formdata)? $this->pivot->formdata: json_decode($this->pivot->formdata, true);


        $data["role"] = $this->role;
        
        $data["delayed"] = (int) $this->delayed; 
        $data["internal"] = (int) $this->internal; 
        $data["featured"]	= (int) $this->featured;
      

        return $data;

    }


}



