<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 
use Eventjuicer\Services\Personalizer;

class ParticipantTicketResource extends Resource
{

    public function toArray($request)
    {   


 		$data = $this->formdata;

        if(is_array($data)){
            $data["participant_id"] = $this->participant_id;
            $data["ticket_id"] = $this->ticket_id;
            // $data["company"] = new PublicCompanyResource($this->participant->company);
            
            $data["company"] = [];
 
            $data["purchase"] = new PurchaseResource($this->purchase);
        }
 		
        return $data;
    }
}



