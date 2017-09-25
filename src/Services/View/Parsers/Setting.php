<?php

namespace Eventjuicer\Services\View\Parsers;

use Eventjuicer\Services\View\Parsers\AbstractParser;

class Setting extends AbstractParser
{
	
	protected function getPrimaryId()
	{

		return $this->getAttribute("name");
	}

	function resolve()
	{

		$settings = \App::make("Contracts\Setting");

		return $settings->get($this->getPrimaryId(), $this->getReplacement());
	}

	public function htmlize($data)
	{

		if($data == $this->getReplacement())
		{
			return $this->hasReplacement() ? $this->getReplacement() : "";
		}

		return $data;
	}
}