<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Vipcodes\ShouldBeExpired;

class CompanyVipCodesResource extends Resource {

    public function toArray($request) {       

      $sbe = new ShouldBeExpired($this->resource);

      $data = [];

      $data["id"] = $this->id;

      $data["organizer_id"] = (int) $this->organizer_id;
      $data["group_id"] = (int) $this->group_id;
      $data["event_id"] = (int) $this->event_id;
      $data["company_id"] = (int) $this->company_id;
      $data["participant_id"] = (int) $this->participant_id;
      $data["code"] = $this->code;
      $data["email"] = trim($this->email);

      $data["participant"] = new CompanyVipcodeVisitorResource($this->participant);

      $data["created_at"] = (string) $this->created_at;
      $data["updated_at"] = (string) $this->updated_at;
      
      $data["blocked_till"] = $data["email"] ?  (string) $sbe->blockedTill() : "";
      
      $data["should_be_expired"] = $sbe->check();

      return $data;

    }


}