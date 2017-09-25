<?php

namespace Eventjuicer\Services\Context;

use Contracts\Context as IContext;

use Illuminate\Http\Request;

use Eventjuicer\Services\Context\Exceptions\InvalidContextResolved;

use View;


class Context implements IContext {
	
	protected $contexts = array();
	protected $apps = array();

	protected $request;
	protected $config;

	function __construct(Request $request,  array $config)
	{
		$this->request = $request;
		$this->config  = $config;
	}

	public function register($name, $class)
	{
		$this->contexts[$name] = new $class($this->request, $this->config);
	}

	public function registerApp($name, $composer, $path = "*")
	{
		$this->apps[strtolower($name)] = ["path" => $path, "composer" => $composer];
	}


	public function create($params = [])
	{
		$this->level()->create($params);

		$this->compose();
	}



	function __call($name, $args = [])
	{
		if(empty($this->contexts[$name]))
		{
			throw new InvalidContextResolved("Bad context {$name} resolved!");
		}

		return $this->contexts[$name];

	}


	public function has($name)
	{
		return !empty($this->contexts[$name]);
	}





	protected function compose()
	{

		$name = strtolower($this->app()->name());
	

		if(!$name)
		{
			return;
		}


		if(isset($this->apps["*"]))
		{
			View::composer($this->apps["*"]["path"], $this->apps["*"]["composer"]);
		}

		if(isset($this->apps[$name]))
		{
			View::composer($this->apps[$name]["path"], $this->apps[$name]["composer"]);
		}

	}







	public function account_default_url()
	{

		return $this->app()->url_for_account($this->user()->account_default());
	}


	public function accounts_urls()
	{
		$names = $this->user()->account_names();

		if($names && $this->app()->host())
		{

			return array_combine($names, array_map(function($account)
			{
					return $this->app()->url_for_account($account);

			}, $names));
			
		}

		return [];
	}


}