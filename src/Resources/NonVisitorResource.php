<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class NonVisitorResource extends Resource{

    static $protectedProfileFields = [
        "phone", 
        "cname",
        "fname",
        "lname",
        "votes_override", 
        "votes_earned", 
        "votes",
        "nip", 
        "company_address",
        "confidential"
    ];

    static $showVotes = false;

    static public function showVotes($bool){
        self::$showVotes = $bool;
    }

    public function toArray($request)
    {
        $data = [];

		$data["id"] = $this->id;
        $data["company_id"] = $this->company_id;
        $data["event_id"] = $this->company_id;
        $data["lang"] = $this->lang;
		$data["profile"] = array_diff_key($this->profile(), array_flip(self::$protectedProfileFields));        

        // $votes_override_field = $this->fieldpivot->where("field_id", 213)->first();
        // $votes_override = !is_null($votes_override_field) ? (int) $votes_override_field->field_value : 0;     
        // $data["votes"] = $this->votes->count() + $votes_override;

        if(self::$showVotes){
             $data["votes"] = $this->_votes;
        }


        $data["roles"] = $this->roles();

		return $data;


            
    }
}



