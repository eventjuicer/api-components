<?php 

namespace Eventjuicer\Services;

use Illuminate\Http\Request;
use Eventjuicer\Repositories\EventRepository;
use Eventjuicer\Repositories\GroupRepository;

use Illuminate\Support\Facades\Auth;

class AdminUser {

	protected $request, $events, $user;

	function __construct(Request $request, EventRepository $events){

		$this->request = $request;
		$this->events = $events;
		$this->user = Auth::user();

	}

	public function active_event_id(){

		return (int) $this->request->input("event_id", 0);
	}

	public function active_group_id(){

		$e = $this->active_event_id();

		return $e ? $this->events->find($e)->group_id : 0;
	}

	public function organizations(){

		return $this->user->organizations;
	}

	public function events(){

		$this->user->load("organizations.events");

		return $this->organizations()->pluck("events")->collapse();

	}

	public function event_ids(){
		
		return $this->events()->pluck("id")->all();
	}

	public function groups(){

		$this->user->load("organizations.groups");

		return $this->organizations()->pluck("groups")->collapse();

	}

	public function group_ids(){

		return $this->groups()->pluck("id")->all();

	}


}