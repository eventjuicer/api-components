<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;
use Eventjuicer\Services\Hashids;
use Eventjuicer\Services\Traits\Fields;

class PublicContestantCompany extends Resource
{

    use Fields;

    protected $showable = array(

        "cname2",
        "company_description",
        "company_website",

        "logotype",
        "logotype_cdn",
        "avatar",
        "avatar_cdn",

        "featured",
        "awards_category",

        "product_name",
        "product_description",
        "project_name",
        "project_description",
        "case_study",
        
        "video",
        "difference",
        "innovations",
        "testimonials",
        
        "winner"

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



