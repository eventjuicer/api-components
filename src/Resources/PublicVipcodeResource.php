<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;
use Eventjuicer\Services\Personalizer;
use Carbon\Carbon;


class PublicVipcodeResource extends Resource{


    public function toArray($request)
    {

            // $profile = new Personalizer($this->resource);

            $data["id"] = (int) $this->id;
            $data["code"] = (string) $this->code;
            $data["expired"] = (int) $this->expired;

            $data["organizer_id"] = (int) $this->organizer_id;
            $data["group_id"] = (int) $this->group_id;
            $data["event_id"] = (int) $this->event_id;
            $data["participant_id"] = (int) $this->participant_id;


            $data["created_at"] = (string) $this->created_at;
            $data["updated_at"] = (string) $this->updated_at;
            
            $data["should_be_expired"] =  !$this->participant_id && Carbon::now()->gt( $this->updated_at->addDay() ) ;


            $data["company"] = new PublicCompanyResource($this->company);

           return $data;
    }
}



