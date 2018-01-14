<?php

namespace Eventjuicer\Exceptions;

use Exception;

class NoCompanyAssignedException extends Exception
{
    public $message = "No company assigned";
}
