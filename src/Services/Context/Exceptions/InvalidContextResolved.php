<?php

namespace Eventjuicer\Services\Context\Exceptions;

//use RuntimeException;

use Symfony\Component\Process\Exception\RuntimeException;

class InvalidContextResolved extends RuntimeException
{
    

    public function __construct($message = null)
    {
        parent::__construct($message);
    }




}
