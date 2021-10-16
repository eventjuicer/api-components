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

        $data["id"] = $this->id;
        $data["company_id"] = $this->company_id;

        $data["account"] = $this->getCompanyAdminInitials();
        $data["profile"] = $this->profileData(["booth", "fname", "lname", "phone", "cname"]);
        $data["company"] = $this->companyData(["name", "keywords", "lang", "password"]);
        $data["reps"] = $this->getReps("representative")->count();
        $data["party"] = $this->getReps("party")->count();
        $data["meetups"] = $this->getMeetups()->count();
        $data["errors"] = $this->getCompanyDataErrors();
        $data["purchases"] = ReportTicketResource::collection($this->getPurchases());
       // $data["ranking"] = $this->

        return $data;
    }


    // protected function hasTicketPivot(){

    //     $p = $this->relationLoaded("participants");

    //     if($p && $this->participants->first() ){
    //         return $this->participants->first()->relationLoaded("ticketpivot");
    //     }

    //     return false;
    // }
}



