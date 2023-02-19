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
            $data["lname"] = $this->stringToSecret($profile->lname);
            $data["cname2"] = (string) $profile->cname2;
            $data["phone"] = str_pad ( substr( (string) $profile->phone, 0, 3) , 9, "*", STR_PAD_RIGHT);
            $data["vip"] = (string) $profile->isVip();
            $data["ns"] = "participant";
            $data["email"] = (new EmailAddress($this->email))->obfuscated();
            $data["code"] = $profile->code;

           return $data;
    }

    private function stringToSecret( $string = NULL)
    {
        if (!$string) {
            return NULL;
        }
        $length = mb_strlen($string);
        $visibleCount = (int) round($length / 4);
        $hiddenCount = $length - ($visibleCount * 2);
        return mb_substr($string, 0, $visibleCount) . str_repeat('*', $hiddenCount) . mb_substr($string, ($visibleCount * -1), $visibleCount);
    }

}



