<?php

namespace Eventjuicer\Services\Cascaded\Exceptions;


class BadKeyException extends \Exception
{
    
    function __construct($key = "")
    {

        parent::__construct($key . " not found in Settings");

    }


}
