<?php

namespace Eventjuicer\Services\View\Widgets;

use App;
use View;

use Eventjuicer\Services\View\Parsers\AbstractParser;

class TagBasedPromotion extends AbstractParser {


	//use Datasource;


	static $reparse = true;


	function resolve()
	{

	}

	function htmlize()
	{

		//dd($this->parentObject);//->tags->pluck("name", "id"));
		return (string) View::make('widgets.tagbasedpromotion', [ "headline" => $this->getAttribute("title")]);

	}


}