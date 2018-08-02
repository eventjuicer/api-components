<?php 

namespace Eventjuicer\Services;

use Illuminate\Http\Request;
use Eventjuicer\Repositories\EventRepository;
use Eventjuicer\Repositories\GroupRepository;


use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;
use Eventjuicer\Repositories\Criteria\WhereIn;


use Illuminate\Support\Facades\Auth;

class AdminUser {

	protected $request, $events, $user;

	function __construct(Request $request, EventRepository $events){

		$this->request = $request;
		$this->events = $events;
		$this->user = Auth::user();

	}

	public function criteria($groupLevel = false){

		$id_like = trim($this->request->input("id_like", ""));

		$active_event_id = $this->active_event_id();

		if(!empty($id_like) && (is_numeric($id_like) || strpos($id_like, "|")!==false)){
			return new WhereIn("id", explode("|", $id_like));
		}

		if($active_event_id > 0){

			if($groupLevel){

				return new BelongsToGroup( $this->active_group_id() );
			}

			return new BelongsToEvent($active_event_id);
		}

		//if none exists...throw an Exception
		
		//throw new \Exception("No Criteria ...");
	}


	public function validate(){

		//either id_like or event_id must be present!
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