<?php 

namespace Eventjuicer\Services\View;


use Illuminate\Http\Request;
use Illuminate\Config\Repository AS Config;

use Illuminate\Database\Eloquent\Model as Eloquent;

use Contracts\Context;

//use Log;

use Contracts\Template as TemplateContract;

use Eventjuicer\Services\Presenter\Presenter;


class Template  implements TemplateContract {
	

	protected $request;
	protected $config;
	protected $context;



	protected $params;

	protected $keys = [];


	private $_instance;


	protected $mappings = array(

		"og.title" 			=> "title", 
		"og:description" 	=> "description",
		"og:type"			=> "website_type",
		"og:url"			=> "permalink",
		"og:site_name"		=> "",
		"og:locale"			=> "locale",
		"fb:admins"			=> "fb-admins",
		"og:image"			=> "thumbnail",
		"image_src"			=> "thumbnail",


	);


	protected $source;

	protected $template;

	protected $parser;

	function __construct(Request $request, Config $config, Context $context)
	{
		$this->request = $request;
		$this->config  = $config["template"];
		$this->context  = $context;
		
		
	}


	public function create($params = [])
	{

		$this->params = $params;




		$post_id = array_get($this->params, "post_id", 0);

		/*post

			"host" => "shopcamp.org.local"
			"slug" => "open-software-czy-saas-jak-nie-popelnic-bledu"
			"post_id" => "1630"
  		*/
	}



	function __get($what){

		return $this->get($what);
	}

	
	function source(Presenter $data, $mapping = "default")
	{

		$className = "Services\View\Mappings\\" . ucfirst($mapping);

		$this->source = class_exists($className) ? new $className($data) : null;

		if(method_exists($this->source, "map"))
		{
			$this->set($this->source->map());
		}
	}


	function set($data)
	{

		foreach((array) $data AS $key => $value)
		{
			$key = str_slug($key);
			$this->keys[ $key ] = $value;
		}



	//dd($this->keys);
	}



	function view()
	{
		
	}


	function template()
	{

		if(!empty($this->params["host"]))
		{

			switch($this->params["host"])
			{
				case "shopcamp.org.local":
				case "klubokawiarnia-pies.pl":

				//	return 'layouts.bootstrap3.carousel';

				break;
			}

		}


		return 'layouts.goodnews.home';
	}

	function get($key, $replacement = "")
	{
		
		$key = str_slug($key);

		if(!isset($this->keys[$key]))
		{
			//LOG!

			 return $replacement;

			//throw new \Exception("template variable - " . $key . " - not found.");
		}


		$str = str_replace(["\n","\r"], "", $this->keys[$key]);

		return addslashes($str);
	}



	function pageheader($args)
	{
	
		return '<div class="breadcrumbs"><h1>'.$this->get("title", $args).'</h1></div>';

	}


/*

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@polak20">
<meta name="twitter:creator" content="@az">
<meta name="twitter:title" content="Podsumowanie 2015 roku i życzenia na 2016 – opinie ekspertów branży e-commerce [cz.1]">
<meta name="twitter:description" content="Kończy się rok i poza naturalnym w handlu zwiększeniem obrotów przychodzi powoli czas podsumowań. Co się udało, co trzeba poprawić i jaką powziąć strategię na kolejny rok. Zadaliśmy te pytania ekspertom branży. Oto ich odpowiedzi: ">
<meta name="twitter:image:src" content="http://static-e.fp20.org/static/posts/1549/e830aa326473038fddf41d7279b09e42b190fcb7.png">


*/


}