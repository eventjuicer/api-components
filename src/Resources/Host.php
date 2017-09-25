<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

use Carbon\Carbon;


class Host extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
            $now = (string) Carbon::now();
            
         return [
     
            "id"            => (int) $this->id,
            "group_id"      => (int) $this->group_id,     
            "organizer_id"  => (int) $this->organizer_id, 
            "active_event_id" => 0,
            "api_endpoint" => "",
            "lastmod"   => [

                "pages"     => $now,
                "configs"   => $now,
                "tickets"   => $now,
                "settings"  => $now,
                "texts"     => $now

            ],

        ];
    }
}
