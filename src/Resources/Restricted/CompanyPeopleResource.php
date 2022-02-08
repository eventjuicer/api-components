<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Personalizer;
use Eventjuicer\Services\Hashids;


class CompanyPeopleResource extends Resource {

    public function toArray($request) {       

    
      $data = [];

      $data["id"] = $this->id;

      $data["fname"] = $this->fname;
      $data["lname"] = $this->lname;
      $data["email"] = $this->email;
      $data["phone"] = $this->phone;
      $data["role"] = $this->role;

      $data["created_at"] = (string) $this->created_at;

      $data["updated_at"] = (string) $this->updated_at;

        return $data;

    }


}