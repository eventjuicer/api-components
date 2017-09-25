<?php

namespace Eventjuicer\Services\View\Exceptions;


class ParserTargetElementNotFoundException extends \Exception
{
	
	function __construct($message = "")
	{

		parent::__construct($message);
	}

}