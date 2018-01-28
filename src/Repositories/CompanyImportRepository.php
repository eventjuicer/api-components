<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\CompanyImport;
use Eventjuicer\Repositories\Repository;
use Eventjuicer\Services\ApiUser;



// use Eventjuicer\Repositories\Criteria\BelongsToCompany;

// use Carbon\Carbon;
// use Uuid;




class CompanyImportRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;



    public function model()
    {
        return CompanyImport::class;
    }



    public function prepare(array $postData, ApiUser $user)
    {


        $name = array_get($postData, "name", "import");
        $manual = (array) array_get($postData, "imported_manually", []);
        $csv = (array) array_get($postData, "imported_json", []);

        $data = [];

        if( ! count($manual) && ! count($csv) )
        {
            return false;
        }

        if( ! $user->company()->id )
        {
            return false;
        }

        //check access to 

        $data["organizer_id"] = $user->company()->organizer_id;
        $data["group_id"] = $user->company()->group_id;
        $data["company_id"] = $user->company()->id;
        $data["user_id"] = $user->user()->id;

        $data["name"] = $name;
        $data["count"] = count($manual);
        $data["data"] = compact("manual", "csv");

        return $data;
    }




}