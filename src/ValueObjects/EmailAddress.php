<?php 

namespace Eventjuicer\ValueObjects;

class EmailAddress  
{
    private $address;

    static $pattern = '/[a-z\d._%+-]+@[a-z\d.-]+\.[a-z]{2,4}\b/i';

    public function __construct($address)
    {
        $this->address = $address;

        $this->normalize();
    }

    public function find(){

            $find = preg_match(self::$pattern, $this->address, $matches);

            if($find && !empty($matches[0]) ){

                $this->address = $matches[0];

                $this->normalize();
            }

            if($this->isValid()){
                
                return $this->address;
            }

            return false;

    }

    protected function normalize(){
        $this->address = strtolower(trim($this->address));
    }

    public function obfuscated($maskWith = "*")
    {
        $len = min(strlen($this->address), 4);

        return substr($this->address, 0, $len) . "---@---" .  substr($this->address, $len * -1);
    }

    public function domain()
    {
        $parts = explode("@", $this->address);

        return $parts[1];
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