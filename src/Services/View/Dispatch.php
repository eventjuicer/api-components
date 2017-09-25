<?php

namespace Eventjuicer\Services\View;

use Contracts\View\Dispatch as DispatchInterface;

use Illuminate\Http\Request;
use Contracts\Context;
use Illuminate\Contracts\Cache\Repository as Cache;
//use Illuminate\Config\Repository AS Config;

use Eventjuicer\Services\View\Exceptions\InvalidBladeExtensionsHandlerException;

use DOMElement;

class Dispatch implements DispatchInterface
{


	protected $clearable = ["@image", "@setting", "@widget","@text"];

	private $image, $setting, $text, $widget;

	private $config;

	protected $parser;

	protected $cache;

	function __construct(Request $request, Context $context, Cache $cache, Template $template, Parser $parser)
	{

		//
		//$this->config = $config;
		$this->cache = $cache;
		$this->template = $template;
		$this->parser = $parser;
	}

	protected function load($parserType, $attrs, $parentObject = null)
	{
		

		// if(!class_exists($parserName))
		// {
		// 	throw new InvalidBladeExtensionsHandlerException();
		// }

	

		if($parserType == "widget")
		{
			$parserName = __NAMESPACE__ . "\Widgets\\" . studly_case($attrs["type"]);

			return new $parserName($attrs, $parentObject, $this->cache, $this->template, $this->parser);
		}

		
		$parserName = __NAMESPACE__ . "\Parsers\\" . ucfirst($parserType);

		return new $parserName($attrs, $parentObject, $this->cache, $this->template, $this->parser);
	}

	public function domElement($parserType, DOMElement $node, $parentObject = null)
	{
		return (string) $this->load($parserType, $this->getNodeAttributes($node), $parentObject);
	}

	public function __call($parserType, $args)
	{
		//dd($args);

		return (string) $this->load($parserType, $args);

	}

	private function getNodeAttributes(DOMElement $node)
	{
		$attributes = array();

		foreach($node->attributes as $attribute_name => $attribute_node)
		{
			$attributes[$attribute_name] = (string) $attribute_node->nodeValue;
		}

		$attributes["innerText"] = $node->nodeValue;

		return $attributes;
	}

	public function cachable(DOMElement $node)
	{
		$attrs = $this->getNodeAttributes($node);

		if(isset($attrs["cache"]))
		{
			return filter_var($attrs["cache"], FILTER_VALIDATE_BOOLEAN);
		}

		return true;
	}



}