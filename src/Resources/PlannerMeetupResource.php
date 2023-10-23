<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PlannerMeetupResource extends Resource
{


    public function toArray($request)
    {

        

        $data = [];

        $data["id"] = $this->id;
        $data["agreed"] =  (int) $this->agreed;
        $data["direction"] = (string) $this->direction;
        $data["message"] = (string) $this->message;
        $data["responded_at"] = (string) $this->responded_at;
        $data["scheduled_at"] = (string) $this->scheduled_at;
        $data["created_at"] = (string) $this->created_at;
        $data["updated_at"] = (string) $this->updated_at;

        $data["presenter"] = new PresenterResource($this->presenter);
        $data["company"] = new PlannerCompanyResource($this->company);

        return $data;



            
    }
}



