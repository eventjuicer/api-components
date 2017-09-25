<?php 

namespace Eventjuicer\Services\Context;



use Illuminate\Http\Request;

use Auth;

use View;


class App {
	

	protected $request;
	protected $config;

	protected $apps = [];


	function __construct(Request $request, array $config)
	{

		$this->request = $request;
		$this->config = $config;
	}


	public function in_admin()
	{
		if($this->request->path() == "admin" || $this->request->is('admin/*'))
		{
			return true;
		}				

		return false;
	}


	public function name()
	{
		preg_match_all('/^((?=.*('.implode("|", $this->config["apps"]).')).*)$/', HOST, $matches);

		if(!empty($matches[2]))
		{
			return $matches[2][0];
		}
		return false;
	}


	public function host()
	{
		return $this->name() ? $this->name() . ".com" : HOST; 
	}


	public function is($name)
	{
		$names = (array) $name;

		return in_array($this->name(), array_map(function($v){return strtolower($v); }, $names));

	}

	public function error_view($code = 404)
	{

		if( isset($this->config["custom_error_pages"][$this->app()]) && 
			isset($this->config["custom_error_pages"][$this->app()][$code]) && 
			View::exists($this->config["custom_error_pages"][$this->app()][$code]))
		{
			return $this->config["custom_error_pages"][$this->app()][$code];
		}

		return 'errors.goodnews.general';
	}



	public function routes()
	{
		if($this->name() && file_exists( app_path('Http/routes.'.$this->name().'.php') ))
		{
			return app_path('Http/routes.'.$this->name().'.php');
		}
		else
		{
			return app_path('Http/routes.hosts.php');
		}
	}

	public function live()
	{
		return config("app.env")=="production";
	}


	public function tld()
	{
		 return  $this->live() ? "" : "." . trim( $this->config["local_environment_suffix"], ".");
	}


	

	public function url_for_account($account)
	{
		if($account && $this->host())
		{
			$url = $account . "." . $this->host() . $this->tld();

			return $this->live() ? "https://" . $url : "http://" . $url;
		}

		return FULLHOST;
	}




	public function lang($locale = "")
	{

		//LANG

		if($locale && strlen($locale)==2)
		{
			\App::setLocale($locale);
		}

		


		//lang taken from URL HOST/lang/.....

		//OR settings

		//OR database 

		return "pl";
	}


	public function lang_fallback()
	{
		//lang taken from URL HOST/lang/.....

		//OR settings

		//OR database 

		return "pl";
	}







}