<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;


class PublicCompanyTicketPivotResource extends Resource
{



    public function toArray($request){   

       
        

        $data = [

            "purchase_id" => $this->purchase_id,        
            "participant_id" => $this->participant_id,        
            "ticket_id" => $this->ticket_id,        
            "formdata" => is_array($this->formdata)? $this->formdata: [],
            "role" => $this->ticket->role,        

            
            
        ];

        
        return $data;
    }


    
}



