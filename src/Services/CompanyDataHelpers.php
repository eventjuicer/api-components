<?php

namespace Eventjuicer\Services;

use Illuminate\Support\Collection;
use Eventjuicer\ValueObjects\EmailAddress;

class CompanyDataHelpers {

    protected $data;
    protected $availableLangs = ["pl", "en", "de"];

	function __construct() {
		
	}

    public function setData(Collection $data){
        $this->data = $data;
    }

    public function manager($type = "event"){

        $arr = $this->toArray();

        $lookup = (new EmailAddress($arr[$type . "_manager"]))->find();

        return !empty($lookup) ? $lookup : "";

    }

    public function lang($default = "") {

        $arr = $this->toArray();

        return !empty($arr["lang"]) && in_array($arr["lang"], $this->availableLangs) ? $arr["lang"] : $default;
    }


    public function toArray() {

        return $this->data->mapWithKeys(function($i){
                
                return [$i->name => $i->value];

        })->all();
    }

}