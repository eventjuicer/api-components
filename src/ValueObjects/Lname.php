<?php 

namespace Eventjuicer\ValueObjects;

class Lname  
{
    private $lname;

    public function __construct($lname)
    {
        $this->lname = trim($lname);

    }

    public function obfuscated($maskWith = "*")
    {

        $strlen = max(mb_strlen($this->lname), 6);

        $mask = round($strlen / 2);

        $str = mb_substr( $this->lname , 0, $mask);

        return str_pad( $str, $strlen, $maskWith, STR_PAD_RIGHT);
    }


    // public function isValid()
    // {
    //     return filter_var($this->lname, FILTER_VALIDATE_EMAIL);
    // }

    public function __toString()
    {
        return $this->lname;
    }

    public function equals(Lname $lname)
    {
        return (string) $this === (string) $lname;
    }
}