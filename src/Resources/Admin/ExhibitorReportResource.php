<?php

namespace Eventjuicer\Resources\Admin;

use Illuminate\Http\Resources\Json\Resource;
 
use Eventjuicer\Services\Cloudinary; 

use Eventjuicer\ValueObjects\CloudinaryImage;


class ExhibitorReportResource extends Resource
{


 

    public function toArray($request)
    {   
        $data = [];

        $data["profile"] = $this->profileData();
        $data["company"] = $this->companyData();
        $data["reps"] = $this->getReps("representative");
        $data["party"] = $this->getReps("party");

        return $data;
    }


    protected function hasTicketPivot(){

        $p = $this->relationLoaded("participants");

        if($p && $this->participants->first() ){
            return $this->participants->first()->relationLoaded("ticketpivot");
        }

        return false;
    }
}



