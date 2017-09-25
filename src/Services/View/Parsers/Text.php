<?php

namespace Eventjuicer\Services\View\Parsers;

use Eventjuicer\Services\View\Parsers\AbstractParser;

class Text extends AbstractParser
{
	static $reparse = true;
	
	protected function getPrimaryId()
	{
		return $this->getAttribute("name");
	}

	function resolve()
	{
		$settings = \App::make("Contracts\Text");

		return $settings->get($this->getPrimaryId(), $this->getReplacement());
	}


	public function htmlize($data)
	{

		if($data == $this->hasReplacement())
		{
			return isset($this->hasReplacement()) ? $this->getReplacement() : "";
		}

		return $data;
	}

}