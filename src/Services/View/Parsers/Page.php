<?php

namespace Eventjuicer\Services\View\Parsers;

use Eventjuicer\Services\View\Parsers\AbstractParser;

class Page extends AbstractParser
{
	
	static $reparse = true;


	function resolve($args)
	{
		$args = func_get_args();

	}

}