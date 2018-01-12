<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;

/*

         "id": 76,
                    "group_id": 1,
                    "organizer_id": 1,
                    "fb_eventid": "",
                    "names": "XII Targi eHandlu",
                    "descriptions": "",
                    "location": "Gdańsk",
                    "starts": "2017-05-10 10:00:00",
                    "ends": "2017-05-10 17:00:00",
                    "upsellable": 0,
                    "bmap": "",
                    "avatar": ""

*/



class ApiUserCompanyEventResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
       return [
            "id"    => (int) $this->id,
            "name" => $this->names,
            "starts" => (string) $this->starts,
            "ends" => (string) $this->ends
           
        ];
    }
}
