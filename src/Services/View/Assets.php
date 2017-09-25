<?php

namespace Eventjuicer\Services\View;

//use Closure;
use Illuminate\Http\Request;

use Exceptions\InvalidAssetHandlerException;
use Exceptions\PlaceholderNotFoundException;
 

 
use Contracts\Context;


class Assets
{

	protected $request, $appcontext;

	protected $storage;

	protected $config;



	protected $keys = [];


	protected $scripts = [];
	protected $stylesheets = [];

	protected $script_paths = [];
	protected $stylesheet_paths = [];

	protected $host = "";


	protected $placeholder_scripts = "<!--ASSETS_SCRIPTS-->";

	protected $placeholder_stylesheets = "<!--ASSETS_STYLESHEETS-->";


	function __construct(Request $request, array $config, Context $context)
	{
		$this->config = $config;


		$this->appcontext = $context->app();
				


		if(!$this->config["use_placeholders"])
		{
			$this->placeholder_stylesheets = "</head>";
			$this->placeholder_scripts = "</body>";
		}


	}

	public function check()
	{
		return (!empty($this->keys));
	}


	public function load($key = "")
	{
		$key = (string) $key;

		if(!in_array($key, $this->keys))
		{
			$this->keys[] = $key;
		}
	}


	private function prepare_output()
	{



		//reorder libs

		$bootstrap 	= array_search("bootstrap", $this->keys); //return index

		if( $bootstrap !== false)
		{
			$lib = $this->keys[$bootstrap];
			array_unshift($this->keys, $lib);
			
		}

		$jquery 	= array_search("jquery", $this->keys); //return index

		if( $jquery !== false)
		{			
			$lib = $this->keys[$jquery];
			array_unshift($this->keys, $lib);
			
		}

		$this->keys =	array_unique($this->keys);



		//translate all keys to file paths

		foreach($this->keys AS $key)
		{
			if(isset($this->config["js"][$key]))
			{
				$this->scripts = array_merge($this->scripts, $this->config["js"][$key]);
			}

			if(isset($this->config["css"][$key]))
			{
				//$this->stylesheets = array_merge($this->stylesheets, $this->config["css"][$key]);
			
				$this->stylesheets = array_merge($this->stylesheets, $this->config["css"][$key]);
			}

		}


		//translate to embeds!

		$this->stylesheet_paths = array_map(function($path){

			return '<link href="'.$this->applyHost($path).'" rel="stylesheet" type="text/css">'; 

		}, $this->stylesheets);


		$this->script_paths = array_map(function($path) {

			


			return '<script type="text/javascript" src="'.$this->applyHost($path).'"></script>';

		}, $this->scripts);

	
		
	}


	private function applyHost($path)
	{

		if(!$this->appcontext->live() || strpos($path, "http")!==false)
		{
			return $path;
		}

		return rtrim($this->config["aws_s3_host"], "/") . $path; 
	}



	private function placeholders_check($content)
	{
		
		if(!empty($this->stylesheets) && strpos($content, $this->placeholder_stylesheets)===false)
		{
			throw new PlaceholderNotFoundException();
		}

		if(!empty($this->scripts) && strpos($content, $this->placeholder_scripts)===false)
		{
			throw new PlaceholderNotFoundException();
		}
	}

	public function merge($content = "")
	{

		$this->placeholders_check($content);

		$this->prepare_output();

		$content = str_ireplace($this->placeholder_stylesheets, $this->placeholder_stylesheets . implode("\n", $this->stylesheet_paths), $content);

		$content = str_ireplace($this->placeholder_scripts, $this->placeholder_scripts . implode("\n", $this->script_paths), $content);

	
		return $content;
	}


	

	
}