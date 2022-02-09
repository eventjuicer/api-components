<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource; 
use Eventjuicer\ValueObjects\EmailAddress;

class PublicPurchaseResource extends Resource {

    public function toArray($request){   

        $data = array();

        $data["id"] = $this->id;
        $data["domain"] = (new EmailAddress($this->email))->domain();
        $data["slug"] = $this->company ? $this->company->slug: null;
        $data["created_at"] = (string) $this->createdon;
        $data["booths"] = $this->ticketpivot->pluck("formdata")->filter();

        return $data;
    }


}



