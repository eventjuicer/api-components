<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AdminEventResource extends Resource
{

    static $groups;


    static public function setGroups($groups){

        self::$groups = $groups;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {


       $group = array_get(self::$groups, $this->group_id, null);

       return [

            "id"        => (int) $this->id,
            "name"      => $this->names,
            "loc"       => $this->location,
            "starts"    => $this->starts,
            "ends"      => $this->ends,

            "is_active" => is_object($group) ? ($group->active_event_id == $this->id) : null
               
        ];
    }
}
