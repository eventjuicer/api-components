<?php 

namespace Eventjuicer\ValueObjects;

class PhoneNumber  
{
    private $number;

    public function __construct($number)
    {
        $this->number = preg_replace("/\s+/", "", $number);
    }

    public function obfuscated()
    {
        $len = min(strlen($this->number), 4);

        return substr($this->number, 0, $len) . "---@---" .  substr($this->number, $len * -1);
    }

    public function isValid()
    {
       // return filter_var($this->address, FILTER_VALIDATE_EMAIL);
    }

    public function __toString()
    {
        return $this->number;
    }

    public function equals(PhoneNumber $number)
    {
        return (string) $this === (string) $number;
    }
}