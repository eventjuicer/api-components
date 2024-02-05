<?php

namespace Eventjuicer\Resources\Services;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Personalizer;

class ApiConnectResource extends Resource {


    public function toArray($request)
    {


        $profile = new Personalizer($this->resource);

        $data["id"] = (int) $this->id;
        $data["fname"] = (string) $profile->fname;
        $data["lname"] = (string) $profile->lname;
        $data["cname2"] = (string) $profile->cname2;
        $data["position"] = (string) $profile->position;
        $data["company_role"] = (string) $profile->company_role;
        $data["participant_type"] = (string) $profile->participant_type;
        $data["vip"] = (string) $profile->isVip();
        $data["tickets"] = $this->tickets->pluck("role");
        $data["event"] = $this->event->names;

        return $data;
    }

  

}



