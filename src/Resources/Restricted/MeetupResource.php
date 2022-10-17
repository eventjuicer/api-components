<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;

//use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;

use Eventjuicer\Services\Hashids;


class MeetupResource extends Resource
{

    //protected $presenterFields = ["fname", "lname", "cname2", "position"];


    public function toArray($request)
    {

        

        $data = [];

        $data["id"] = $this->id;
        $data["creative_id"] = $this->creative_id;
        $data["agreed"] =  (int) $this->agreed;
        $data["retries"] = (int) $this->retries;
        $data["message"] = $this->message;
        $data["comment"] = $this->comment;
        $data["sent_at"] = (string) $this->sent_at;
        $data["resent_at"] = (string) $this->resent_at;
        $data["responded_at"] = (string) $this->responded_at;
        $data["scheduled_at"] = (string) $this->scheduled_at;
        $data["created_at"] = (string) $this->created_at;
        $data["updated_at"] = (string) $this->updated_at;

        $data["direction"] = (string) $this->direction;

        if($this->agreed){
            $data["participant"] = new FullVisitorResource($this->participant);
        }else{
            $data["participant"] = new VisitorResource($this->participant);
        }


        $data["admin"] = new ApiUserResource($this->admin, true);


        return $data;

/*
        return [


                      "id": 1,
            "organizer_id": 1,
            "group_id": 1,
            "company_id": 6,
            "participant_id": 50066,
            "user_id": 50067,
            "creative_id": 0,
            "agreed": 0,
            "retries": 0,
            "message": "Spotkajmy się!",
            "comment": "",
            "sent_at": null,
            "resent_at": null,
            "responded_at": null,
            "scheduled_at": null,
            "created_at": "2018-01-12 00:00:00",
            "updated_at": "2018-01-12 00:00:00",
            "participant": {
                "fname": "Natalia",
                "lname": "Paczóska",
                "cname2": "GetResponse",
                "id": 50066,
                "ns": "participant",
                "email": "hann---@---.com",
                "code": "paxev"
            },
            "admin": {
                "fname": "Agnieszka",
                "lname": "Fabianowicz",
                "cname2": "Blue Media",
                "id": 50067,
                "ns": "participant",
                "email": "agni---@---a.pl",
                "code": "mzkek"
            }

        ];

*/


     



            
    }
}



