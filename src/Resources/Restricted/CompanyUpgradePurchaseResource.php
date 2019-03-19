<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Resources\PurchaseResource;


class CompanyUpgradePurchaseResource extends Resource
{



    public function toArray($request)
    {       

        $data = [];
        
        $data["quantity"]   = $this->quantity;
        $data["buyer"]      = $this->participant->email;
        $data["status"]     = $this->purchase->status;
        $data["total"]      = $this->purchase->amount;
        $data["finalized"]  = $this->purchase->paid;
        $data["ts"]         = (string) $this->purchase->updatedon;
        
        return $data;

    }


}



