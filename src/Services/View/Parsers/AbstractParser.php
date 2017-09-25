<?php

namespace Eventjuicer\Services\View\Parsers;

use Closure;


use Illuminate\Contracts\Cache\Repository;

use Eventjuicer\Services\View\Exceptions\ParserTargetElementMisconfiguredException;

use Contracts\Template;
use Contracts\View\Parser;

use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class AbstractParser {
	
	

	protected $translated = "";

	protected $replacement = "<!--replacement-->";

	protected $nodeAttributes;
	protected $nodeValue;
	
	static $reparse = false;

	protected $parentObject = null;
	protected $cache;
	protected $template;
	protected $parser;

	final function __construct(array $nodeAttributes, $parentObject = null, Repository $cache, Template $template, Parser $parser)
	{

		$this->nodeAttributes 	= $nodeAttributes;
		$this->parentObject 	= $parentObject;

		if(!is_null($parentObject) && !$parentObject instanceOf Eloquent)
		{
			throw new \Exception("parent object is not valid Eloquent model");
		}

		$this->cache = $cache;

		$this->template = $template;

		$this->parser = $parser;

		if(!$this->getPrimaryId())
		{
			throw new ParserTargetElementMisconfiguredException("No id/name attribute given!");
		}

		$this->translated = $this->cached( function(){ 
			return $this->resolve(); } 
		);	
	}


	final protected function hasAttribute($what)
	{
		return isset($this->nodeAttributes[$what]);
	}

	final protected function getAttribute($what)
	{
		return isset($this->nodeAttributes[$what]) ? $this->nodeAttributes[$what] : "";
	}

	final protected function getAttributes()
	{
		return $this->nodeAttributes;
	}

	final protected function getInnerText()
	{
		return $this->getAttribute("innerText");
	}

	protected function getPrimaryId()
	{
		if($this->hasAttribute("id"))
		{
			return $this->getAttribute("id");
		}

		if($this->hasAttribute("type") && $this->hasAttribute("name"))
		{
			return $this->getAttribute("name") . "-" . $this->getAttribute("type");
		}

		return false;
	}

	protected function hasReplacement()
	{
		return $this->replacement != "<!--replacement-->";
	}

	protected function getReplacement()
	{
		return $this->replacement;
	}


	abstract protected function resolve();


	final protected function cacheKey()
	{
		return strtolower(class_basename(get_called_class())) ."-" .  str_slug($this->getPrimaryId());
	}


	final protected function cached(Closure $closure )
	{
		 return $this->cache->remember($this->cacheKey(), 10, $closure);
	}

	final protected function parse($input)
	{
		return $this->parser->parseString($input, $this->parentObject);
	}

	/*function __get($what)
	{
		return isset($this->nodeAttributes[$what]) ?: $this->nodeAttributes[$what];
	}*/


	final function __toString()
	{
		if(method_exists($this, "htmlize"))
		{

			$output = $this->htmlize($this->translated);

		}
		else
		{
			$output = $this->translated;
		}

		return static::$reparse ? (string) $this->parse($output) : (string) $output;
		
	}


}