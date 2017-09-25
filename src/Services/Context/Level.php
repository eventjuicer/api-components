<?php 


namespace Eventjuicer\Services\Context;

use Eventjuicer\Models\Organizer;
use Eventjuicer\Models\Host;
use Eventjuicer\Models\Event;
use Eventjuicer\Models\Group;


use Illuminate\Support\Collection;
use Eventjuicer\Services\Context\Exceptions\DomainNotFoundException;
use Eventjuicer\Services\Context\Exceptions\ProjectNotFoundException;

use Illuminate\Database\Eloquent\Model;

class Level 
{


	//original Route params taken from routing :)
	private $params = array();

	private $context = array(

		"organizer_id" 	=> 0,
		"group_id"		=> 0,
		"event_id"		=> 0,
		"portal_id"		=> 0

	);

	private $context_models = array(
		"organizer" => null,
		"group" 	=> null,
		"event"		=> null
	);

	protected $organizer, $group, $event, $portal;

	protected $config = [];
	protected $request;

	function __construct(\Illuminate\Http\Request $request,  array $config)
	{

		$this->request 	= $request;
		$this->config 	= $config;
		
	}

	
	/*

	fire up by EventServiceProvider!
	
	*/

	public function create($params = [])
	{

		
		$this->params 	= $params;



		if(empty($this->params))
		{
			return false;
		}
		

		//TODO

		/*
	
		foreach($this->params AS $paramName => $paramValue)
		{
			$name = "Resolvers\\" . studly_case($paramName);

			$resolved = new {$name}($paramValue);
		}

		*/


		if( !empty($this->params["account"]))
		{
	
			
			$organizer = Organizer::where("account", "=", $this->params["account"])->firstOrFail();

			$this->context_models["organizer"] 	= $organizer;
			$this->context["organizer_id"] 		= $organizer->id;
		}

		//PUBLIC OR INTERNAL ACCESS


		if( !empty($this->params["project"]) || !empty($this->params["host"]) )
		{			

			if(!empty($this->params["project"]))
			{

				$group = Group::where("slug", "=", $this->params["project"])->where("organizer_id", $this->context["organizer_id"])->first();	

				if(empty($group))
				{

					throw new ProjectNotFoundException();
				}

				$this->context_models["group"] = $group;

			}
			else if( !empty($this->params["host"]))
			{




				$host = Host::find($this->params["host"]);
					
				if(empty($host) || empty($host->group_id))
				{
					throw new DomainNotFoundException();
				}

				$this->context_models["group"]  	= $host->group;
				$this->context_models["organizer"] 	= $this->context_models["group"]->organizer;
				$this->context["organizer_id"] 		= $this->context_models["group"]->organizer_id;



			}

			//IMPORTANT... group_id should be always present!

			if($this->context_models["group"]->is_portal)
			{
				$this->context["portal_id"] = $this->context_models["group"]->id;
			}

			$this->context["group_id"] = $this->context_models["group"]->id;


		}
		

		if( !empty($this->params["event_id"]))
		{

			$event = Event::where("id", "=", $this->params["event_id"])->firstOrFail();

			$this->context_models["event"] = $event;

			$this->context["event_id"] = $event->id;
		}
		


	}

	public function findActiveEvent()
	{

		if($this->context["group_id"])
		{
			//find active event id 

			return ( new FindActiveEvent( $this->context_models["group"] ) )->getModel();

			//$this->context["event_id"] = $this->context_models["event"]->id;
			

		}	

		return null;
		
	}


	function __call($what, $params)
	{
		$what = str_replace("get_", "", $what);

		return $this->get($what);
	}

	public function contextModels()
	{
		return $this->context_models;
	}

	public function params()
    {
    	return $this->params;
    }

	public function current()
    {
    	return $this->context;
    }


    /**
		Returns topmost model ... check event,group and organizer
    **/

    public function model()
    {

    	$model = false;

    	foreach(array_reverse($this->config["levels"]) AS $level)
    	{

    		if(!empty($this->context_models[$level]) && $this->context_models[$level] instanceof Model)
    		{
    			$model = $this->context_models[$level];
    			break;
    		}

    	}

    	return $model;

    }


    public function modelName()
    {
    	return strtolower( (new \ReflectionClass($this->model()))->getShortName() ); 
    }



 	public function getModel($key)
    {
    	return !empty($this->context_models[$key]) ? $this->context_models[$key] : null;
    }




    public function get($key = "", $from_model = null)
    {

    	if(strpos($key, "_id")!==false)
    	{

			if(is_object($from_model) && $from_model instanceof Model && isset($from_model->$key ) )
    		{
    			return (int) $from_model->$key;
    		}
    		else if(isset($this->context[$key]))
    		{
    			return (int) $this->context[$key];
    		}

    	}
    	else if( isset($this->context_models[$key]) )
    	{
    		return $this->context_models[$key];
    	}

    }

    //router
  	public function getParameter($key = "")
    {
    	return isset($this->params[$key]) ? $this->params[$key] : "";
    }

    function __toString()
    {
    	return (string) $this->get_mode();
    }

}