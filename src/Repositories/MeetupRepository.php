<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Meetup;
use Eventjuicer\Repositories\Repository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;

use Carbon\Carbon;
use Uuid;

use Eventjuicer\Services\ApiUser;


class MeetupRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return Meetup::class;
    }


    public function validateHash()
    {

        // sha1()

    }




    public function prepare(array $postData, ApiUser $user)
    {

        $participant_id = array_get($postData, "participant_id", 0);
        $creative_id = array_get($postData, "creative_id", 0);
        $message = array_get($postData, "message", "");
        $json = array_get($postData, "data", []);

        $data = [];

        if( !$participant_id )
        {
            return false;
        }

        if($creative_id && !$user->company()->creatives()->find($creative_id) )
        {
            return false;
        }

        //check access to 

        $data["organizer_id"] = $user->company()->organizer_id;
        $data["group_id"] = $user->company()->group_id;
        $data["company_id"] = $user->company()->id;
        $data["participant_id"] = $participant_id;
        $data["user_id"] = $user->user()->id;
        $data["creative_id"] = $creative_id;
        $data["agreed"] = 0;
        $data["retries"] = 0;
        $data["message"] = $message; 
        $data["comment"] = "";
        $data["data"] = $json;

        return $data;
    }




}