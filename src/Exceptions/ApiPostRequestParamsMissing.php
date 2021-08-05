<?php

namespace Eventjuicer\Exceptions;

use Exception;

class ApiPostRequestParamsMissing extends Exception{
    public $message = "No company assigned";
}
