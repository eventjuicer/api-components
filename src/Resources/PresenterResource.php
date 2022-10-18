<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;
use Eventjuicer\Services\Hashids;
use Eventjuicer\Services\Traits\Fields;


class PresenterResource extends Resource{   


    use Fields;

    static $showVotes = false;

    protected $showable = array(
        "fname",
        "lname",
        "presenter",
        "cname2",
        "position",
        "presentation_title",
        "presentation_description",
        "presentation_venue",
        "presentation_time",
        "presentation_day",
        "presentation_category",
        "avatar",
        "logotype",
        "avatar_cdn",
        "logotype_cdn",
        "bio",
        "featured",
        "custom_admin_1",
        "cfp_category",
        "featured_cfp",
        "video_length_minutes",
        "video_is_public",
        "video",
        "profile_twitter",
        "profile_linkedin",
        "profile_facebook",
        "limited"
    ); 

    static public function showVotes($bool){
        self::$showVotes = $bool;
    }

    //votes_override = 213!!!

    public function toArray($request)
    {

        if( ! $this->relationLoaded("fieldpivot") ){
          
            throw new \Exception("Use fieldpivot");

        }

        $data = $this->filterFields($this->fieldpivot, $this->showable);

        if(self::$showVotes){
             $data["votes"] = $this->_votes;
        }

        $data["id"] = (int) $this->id;
        $data["company_id"] = (int) $this->company_id;
        $data["event_id"] = (int) $this->event_id;
        $data["event"] = new PublicEventResource($this->whenLoaded("event"));

        return $data;
    }
}



