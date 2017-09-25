<?php

namespace Eventjuicer\Services\Cascaded\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class KeyNotFoundException extends NotFoundHttpException
{
    
    function __construct($key = "")
    {

        parent::__construct($key . " not found in Settings");

    }


}
