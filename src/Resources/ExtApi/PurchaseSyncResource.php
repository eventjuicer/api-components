<?php

namespace Eventjuicer\Resources\ExtApi;

use Illuminate\Http\Resources\Json\Resource;


class PurchaseSyncResource extends Resource
{

    public function toArray($request)
    {

        $data = [];
        $data["id"] = (int) $this->id;
        $data["ts"] = (string) $this->updatedon;

        return $data;

        
    }
}



