<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Personalizer;
use Eventjuicer\Services\Hashids;


class CompanyRepresentativeResource extends Resource
{

    public function toArray($request)
    {       

      $codeId = (new Hashids())->encode($this->id);
	  $token = substr($this->token, 0, 5);


      $data = [];

      $data["id"] = $this->id;
      $data["token"] = $this->token;

      $data["profile"] = ( new Personalizer($this->resource) )->getProfile();
      $data["profile"]["email"] = $this->email;
      $data["profile"]["unsubscribed"] = (int) $this->unsubscribed;

      $data["mobileappcode"] = $codeId . "@" . $token;

      $data["created_at"] = (string) $this->createdon;
      $data["updated_at"] = (string) $data["created_at"];

      return $data;

    }


}