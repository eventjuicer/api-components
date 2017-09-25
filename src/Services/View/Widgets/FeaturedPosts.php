<?php

namespace Eventjuicer\Services\View\Widgets;

use App;
use View;

use Eventjuicer\Services\View\Parsers\AbstractParser;

class FeaturedPosts extends AbstractParser {

	static $reparse = true;

	function resolve()
	{

	}

	function htmlize()
	{
		return (string) View::make('widgets.tagbasedpromotion', [ "headline" => $this->getAttribute("title")]);

	}


}