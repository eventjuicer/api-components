<?php 

namespace Eventjuicer\ValueObjects;

class Phone 
{
    private $number;

    public function __construct($number)
    {
        $this->number = preg_replace("/\s+/", "", $number);
    }

    public function obfuscated($maskWith = "*")
    {
        if(!$this->number)
        {
            return str_repeat($maskWith, 9);
        }

        $strlen = max(9, mb_strlen($this->number));

        $str = mb_substr( $this->number , 0, floor( $strlen / 2));

        return str_pad( $str, $strlen, $maskWith, STR_PAD_LEFT);
    }

    public function isValid()
    {
       // return filter_var($this->address, FILTER_VALIDATE_EMAIL);
    }

    public function __toString()
    {
        return $this->number;
    }

    public function equals(Phone $number)
    {
        return (string) $this === (string) $number;
    }
}