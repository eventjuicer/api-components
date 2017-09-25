<?php

namespace Eventjuicer\Services\View\Widgets;

use App;
use View;


use Eventjuicer\Services\View\Parsers\AbstractParser;

class Button extends AbstractParser {


	static $reparse = true;

	function resolve()
	{
		
	}



	function htmlize()
	{
		

		return (string) View::make("sender.button", 
			array("link"=>$this->getAttribute("link"), 
				"text"=>$this->getAttribute("text")));

	}


}