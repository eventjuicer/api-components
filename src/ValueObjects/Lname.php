<?php 

namespace Eventjuicer\ValueObjects;

class Lname  
{
    private $lname;

    public function __construct($lname)
    {
        $this->lname = trim($lname);
    }

    public function obfuscated()
    {
        $len = min(strlen($this->address), 4);

        return substr($this->address, 0, $len) . "---@---" .  substr($this->address, $len * -1);
    }

    public function isValid()
    {
        return filter_var($this->lname, FILTER_VALIDATE_EMAIL);
    }

    public function __toString()
    {
        return $this->lname;
    }

    public function equals(Lname $lname)
    {
        return (string) $this === (string) $lname;
    }
}