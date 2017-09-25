<?php

namespace Eventjuicer\Services\View\Widgets;

use App;
use View;

use Eventjuicer\Services\View\Parsers\AbstractParser;



class Html extends AbstractParser {

	static $reparse = true;

	function resolve()
	{
		
	}


	function htmlize()
	{
		$html = "";

		//return "html";

		return (string) View::make("widgets.html", array("html"=>$html, "name" => "asda"));

	}


}