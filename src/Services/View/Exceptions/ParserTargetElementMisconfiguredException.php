<?php

namespace Eventjuicer\Services\View\Exceptions;


class ParserTargetElementMisconfiguredException extends \Exception
{
	
	function __construct($message = "")
	{

		parent::__construct($message);
	}

}