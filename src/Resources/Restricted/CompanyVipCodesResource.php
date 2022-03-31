<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;
// use Eventjuicer\Services\Personalizer;
// use Eventjuicer\Services\Hashids;


class CompanyVipCodesResource extends Resource {

    public function toArray($request) {       


      $data = [];

      $data["id"] = $this->id;

      $data["organizer_id"] = (int) $this->organizer_id;
      $data["group_id"] = (int) $this->group_id;
      $data["event_id"] = (int) $this->event_id;
      $data["company_id"] = (int) $this->company_id;
      $data["participant_id"] = (int) $this->participant_id;
      $data["code"] = $this->code;

    //   $data["participant"] = 

      
      $data["created_at"] = (string) $this->created_at;
      $data["updated_at"] = (string) $this->updated_at;

        return $data;

    }


}