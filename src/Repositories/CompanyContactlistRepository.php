<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\CompanyContactlist;

use Eventjuicer\Repositories\Repository;

 use Eventjuicer\Services\ApiUser;


class CompanyContactlistRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;


    public function model()
    {
        return CompanyContactlist::class;
    }


    public function firstOrCreate(ApiUser $user)
    {


        $exists = $this->findWhere([

            ["company_id", $user->company()->id]

        ])->first();

        if($exists)
        {
            return $exists;
        }


        $contactlist =  $this->makeModel();

        $data = $this->prepare([], $user);

        if($data && $this->saveModel($data))
        {
            return $contactlist;
        }

        return null;
    }



    public function prepare(array $postData, ApiUser $user)
    {


        $name = array_get($postData, "name", "Main list");
      
        $data = [];

        if( ! $user->company()->id )
        {
            return false;
        }

        //check access to 

        $data["organizer_id"] = $user->company()->organizer_id;
        $data["group_id"] = $user->company()->group_id;
        $data["company_id"] = $user->company()->id;
        $data["user_id"] = $user->user()->id;
        $data["name"] = mb_substr($name, 0, 100);
      

        return $data;
    }




}