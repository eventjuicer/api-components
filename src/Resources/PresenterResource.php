<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;
use Eventjuicer\Services\Hashids;
use Eventjuicer\Services\Traits\Fields;


class PresenterResource extends Resource
{   


    use Fields;

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
        "presentation_category",
        "avatar",
        "logotype",
        "avatar_cdn",
        "logotype_cdn",
        "bio",
        "featured",
        "custom_admin_1",
        "cfp_category",
        "featured_cfp"
    ); 


    //votes_override = 213!!!

    public function toArray($request)
    {

        if( ! $this->relationLoaded("fieldpivot") ){
          
            throw new \Exception("Use fieldpivot");

        }

        $data = $this->filterFields($this->fieldpivot, $this->showable);

        $votes_override_field = $this->fieldpivot->where("field_id", 213)->first();
        $votes_override = !is_null($votes_override_field) ? (int) $votes_override_field->field_value : 0;
                  
        $data["votes"] = $this->relationLoaded("votes") ? $this->votes->count() + $votes_override : 0;

        $data["id"] = (int) $this->id;

        $data["event"] = new PublicEventResource($this->whenLoaded("event"));

        return $data;
    }
}



