<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PublicPostResource extends Resource
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

            "id"        => (int) $this->id,

            "admin_id"      => $this->admin_id,
            "editor_id"      => $this->editor_id,

            "is_published" => $this->is_published,
            "is_sticky"    => $this->is_sticky,
            "is_promoted"  => $this->is_promoted,
            "interactivity" => $this->interactivity,
            "created_at" => (string) $this->created_at,
            "updated_at" => (string) $this->updated_at,
            "published_at" => (string) $this->published_at,


            "headline" => (string) $this->meta->headline,
            "quote_parsed" => (string) $this->meta->quote_parsed,
            "body_parsed" => (string) $this->meta->body_parsed,

            "guestauthor" => (string) $this->meta->guestauthor,
            "metatitle" => (string) $this->meta->metatitle,
            "metadescription" => (string) $this->meta->metadescription,


        ];
    }
}
