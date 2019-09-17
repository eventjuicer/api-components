<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

/*
[
    {
        "id": 1,
        "participant_id": 1111,
        "widget_id": 1,
        "voteable_type": "Eventjuicer\\Models\\SocialLinkedin",
        "voteable_id": 4,
        "organizer_id": 1,
        "group_id": 1,
        "created_at": "2019-09-04 00:00:00",
        "updated_at": "2019-09-04 00:00:00"
    }
]
*/
class SocialVoteResource extends Resource
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
            "id"                => $this->id,
            "participant_id"    => (int) $this->participant_id,
            "participant"       => new SocialVoteParticipantResource($this->participant),
            "created_at"        => (string) $this->created_at
        ];
    }
}
