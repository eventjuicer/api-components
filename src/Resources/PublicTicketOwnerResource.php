<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\ValueObjects\CloudinaryImage;


class PublicTicketOwnerResource extends Resource {


    public function toArray($request){

        $data = [];

        $data["id"]      = $this->id;
        $data["ticket_group_id"] = $this->ticket_group_id;
        $data["event_id"]   = $this->event_id;

        $data["names"]      = $this->names;
        $data["role"]       = $this->role;

        $data["thumbnail"]    = (string) $this->thumbnail;
        $data["image"]    = (string) $this->image;

        $data["translation_asset_id"] = (string) $this->translation_asset_id;
        $data["details_url"] = (string) $this->details_url;
        $data["json"]       = $this->json;

        $data["owners"] = NonVisitorResource::collection($this->participantsNotCancelled);    

        $data["delayed"] 	= (int) $this->delayed;
        $data["featured"]	= (int) $this->featured;
       

        $data["ticket_group"] = $this->group ? array(

            "id"                => $this->group->id,        
            "name"              => $this->group->name,
            "json"      => $this->group->json,

        ) : [];
           
        // "og_image" => (string) (new CloudinaryImage($this->_cover))->wrapped("ehandel_cl_tmpl", "c_fill,w_1000", "x_-100,y_0")
        

        return $data;

    }
}
