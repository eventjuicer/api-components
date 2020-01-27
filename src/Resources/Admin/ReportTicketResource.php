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
        $data["___name"] = array_get($this->names, "en");
        $data["quantity"] = $this->pivot->quantity;
        $data["role"] = $this->role;
        $data["delayed"] = $this->delayed; 
        $data["internal"] = $this->internal;
        
        return $data;

    }


}



