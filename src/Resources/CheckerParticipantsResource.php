<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class CheckerParticipantsResource extends Resource
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
            // "name" => array_get($this->names, "pl"),
            // "names" => $this->names,
            // "translation_asset_id" => $this->translation_asset_id,
            // "role" => $this->role
        ];
    }
}
