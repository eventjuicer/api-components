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

        $data["account"] = $this->admin;
        $data["profile"] = $this->profileData(["fnameXXX","lnameXXX","phoneXXX","booth"]);
        $data["company"] = $this->companyData(["name", "event_manager", "pr_manager", "keywords", "lang"]);
        $data["reps"] = $this->getReps("representative")->count();
        $data["party"] = $this->getReps("party")->count();
        $data["errors"] = $this->getCompanyDataErrors();
        $data["purchases"] = [];
        
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



