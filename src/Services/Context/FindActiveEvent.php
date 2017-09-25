<?php

namespace Eventjuicer\Services\Context;


use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Organizer;
use Eventjuicer\Group;
use Eventjuicer\Event;


class FindActiveEvent {

	protected $group;

	protected $id = 0;
	protected $params;
	protected $model;


	function __construct(Model $group)
	{

		$this->group = $group;

		//find active

		$this->resolve();
		
		
	}


	function resolve()
	{

	
		if($this->group->active_event_id)
		{
			$this->model = Event::where("id", "=", $this->group->active_event_id)->first();

			if(is_null($this->model))
			{
				throw new Exceptions\InvalidContextResolved("No such event when retrieving active_event_id");
			}

			$this->id = $this->model->id;
		}
		else
		{
			//do we have any events attached?

			if($this->group->events->count())
			{
				$this->model = $group_events->sortByDesc("id")->first();	
			}
			else
			{
				$this->model 				= new Event;
           	 	$this->model->names 		= $this->group->name . " #1";
            	$this->model->organizer_id 	= $this->group->organizer_id;          	
            	$this->group->events()->save($this->model);
            	
			}
		
			$this->id = $this->model->id;	

			$this->group->active_event_id = $this->id;
            $this->group->save();

		}

	}

	function getModel()
	{
		return $this->model;
	}


	function __toString()
	{
		return (string) $this->id;
	}


}
