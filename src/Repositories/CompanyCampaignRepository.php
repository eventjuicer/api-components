<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Repositories\Repository;
use Eventjuicer\Models\CompanyCampaign;

 

class CompanyCampaignRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;


    public function model()
    {
        return CompanyCampaign::class;
    }



    // public function prepare(array $postData)
    // {


    //     $email = new EmailAddress( trim(array_get($postData, "email"), ",") );

    //     if(!$email->isValid())
    //     {
    //         return false;
    //     }

    //     $exists = $this->findWhere([
    //         ["email", "like", (string) $email],
    //         ["company_id", array_get($postData, "company_id")]

    //     ])->first();

    //     //we may of course update the record but... not now!
    //     if($exists)
    //     {
    //         return false;
    //     }


    //     $data = [];      

    //     $data["organizer_id"]   = array_get($postData, "organizer_id");
    //     $data["group_id"]       = array_get($postData, "group_id");
    //     $data["company_id"]     = array_get($postData, "company_id");
    //     $data["import_id"]      = array_get($postData, "import_id");

    //     $data["email"]          = (string) $email;
    //     $data["data"]         = (array) array_get($postData, "data", []);


    //     return $data;


    // }




}