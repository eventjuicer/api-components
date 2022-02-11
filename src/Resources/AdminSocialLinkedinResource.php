<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AdminSocialLinkedinResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
       
        $profile = [];
        
        $profile["id"] = $this->id;
        $profile["email"] = $this->email;
        $profile["avatar"] = $this->avatar;
        $profile["fname"] = $this->fname;
        $profile["lname"] = $this->lname;
        $profile["locale"] = $this->locale;
        $profile["created_at"] = (string) $this->created_at;

    
        return $profile;
    }
}




