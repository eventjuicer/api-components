<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Personalizer;

class CompanyRepresentativeResource extends Resource
{

    public function toArray($request)
    {       

          $data = [];

          $data["id"] = $this->id;

          $data["profile"] = ( new Personalizer($this->resource) )->getProfile();

          $data["created_at"] = (string) $this->createdon;

          $data["updated_at"] = (string) $data["created_at"];

          return $data;

    }


}