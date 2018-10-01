<?php 

namespace Eventjuicer\Services;

use Illuminate\Http\Request;
use Eventjuicer\Repositories\EventRepository;
use Eventjuicer\Repositories\GroupRepository;


use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;
use Eventjuicer\Repositories\Criteria\Dumb;

use Eventjuicer\Repositories\Criteria\WhereIn;


use Illuminate\Support\Facades\Auth;

class AdminUser {

	protected $request, $events, $user, $group_ids;

	function __construct(Request $request, EventRepository $events){

		$this->request = $request;
		$this->events = $events;
		$this->user = Auth::user();

		//we will for an authorization and scope...
		$this->user->load("organizations.groups");

		$this->group_ids = $this->group_ids();

	}


	public function criteria($groupLevel = false){

		//validate group?id_like=1|2|3|4|5!

		$id_like = $this->request->input("id_like", false);

		if(!empty($id_like) && (is_numeric($id_like) || strpos($id_like, "|")!==false)){
			return new WhereIn("id", explode("|", $id_like));
		}

		$active_event_id = $this->active_event_id();
		$active_group_id = $this->active_group_id();

		if($active_event_id){

			$groupId = $this->events->find($active_event_id)->group_id;

			if(!$this->check($groupId)){
				throw new \Exception("you cannot access this resource");
			}

			if($groupLevel){

				return new BelongsToGroup($groupId);
			}

			return new BelongsToEvent($active_event_id);
		}
		
		if($active_group_id){

			if(!$this->check($active_group_id)){
				throw new \Exception("you cannot access this resource");
			}

			return new BelongsToGroup( $active_group_id );
		}

		return new WhereIn("group_id", $this->group_ids );

	}

	public function canAccess(){
		return true;
	}

	public function check($groupId){

		return in_array($groupId, $this->group_ids);
	}

	public function active_event_id(){

		return (int) $this->request->input("event_id", 0);
	}

	public function active_group_id(){

		return (int) $this->request->input("group_id", 0);

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

		return $this->organizations()->pluck("groups")->collapse();

	}

	public function group_ids(){

		return $this->groups()->pluck("id")->all();

	}


}