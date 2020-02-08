<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;

use Eventjuicer\Models\Participant;
use Eventjuicer\ValueObjects\EmailAddress;
use Eventjuicer\Services\Personalizer;

class PublicVisitor extends Resource
{


    public function toArray($request)
    {

            $profile = new Personalizer($this->resource);

            $data["id"] = (int) $this->id;
            $data["fname"] = (string) $profile->fname;
            $data["cname2"] = (string) $profile->cname2;
            $data["phone"] = str_pad ( substr( (string) $profile->phone, -3) , 9, "*", STR_PAD_LEFT);
            $data["vip"] = (string) $profile->isVip();
            $data["ns"] = "participant";
            $data["email"] = (new EmailAddress($this->email))->obfuscated();
            $data["code"] = $profile->code;

           return $data;
    }
}



