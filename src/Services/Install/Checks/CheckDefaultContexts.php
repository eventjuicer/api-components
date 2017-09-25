<?php 

namespace Eventjuicer\Services\Install\Checks;

use Eventjuicer\Context;

class CheckDefaultContexts {

	protected $result = false;

	function __construct()
	{

		

	}

	function __toString()
	{
		return (string) $this->result;
	}


}