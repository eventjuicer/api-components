<?php 

namespace Eventjuicer\ValueObjects;

class PhoneNumber  
{
    private $address;

    public function __construct($address)
    {
        $this->address = strtolower(trim($address));
    }

    public function obfuscated()
    {
        $len = min(strlen($this->address), 4);

        return substr($this->address, 0, $len) . "---@---" .  substr($this->address, $len * -1);
    }

    public function isValid()
    {
        return filter_var($this->address, FILTER_VALIDATE_EMAIL);
    }

    public function __toString()
    {
        return $this->address;
    }

    public function equals(EmailAddress $address)
    {
        return (string) $this === (string) $address;
    }
}