<?php 

namespace Eventjuicer\Services;

use Eventjuicer\Models\Host;
use Eventjuicer\Models\Group;
use Eventjuicer\Models\Event;


class Resolver {
	

	protected $host;
	protected $organizer_id = 0, $groupId = 0, $activeEventId = 0;


	function __construct(string $host = "")
	{
		if(!empty($host)){
			$this->setHost($host);
		}
	}

	public function setHost(string $host){

		$this->host = strtolower(trim($host));

		$this->resolve();
	}

	public function fromGroupId($group_id){
		
		$host = Host::where('group_id', $group_id )->firstOrFail()->host;

		$this->setHost($host);

		return $host;
	}


	protected function resolve()
	{

		$this->groupId 		= Host::where('host', "like", $this->host )->firstOrFail()->group_id;

		$query = Group::findOrFail($this->groupId);

		$this->organizer_id = (int) $query->organizer_id;

		return $this->activeEventId = (int) $query->active_event_id;
	}

	public function getGroupId()
	{
		return $this->groupId;
	}

	public function getOrganizerId()
	{
		return $this->organizer_id;
	}


	public function previousEvent()
	{
		$allEvents = Event::where("group_id", $this->getGroupId())->orderBy("id", "desc")->get()->pluck("id")->all();

		$prev = 0;

        foreach($allEvents as $event){
            if( $event == $this->getEventId() ){
                $prev = next($allEvents);
                break;
            }
        }

        return $prev;

	}

	public function getEventId()
	{
		return $this->activeEventId;
	}

	public function __toString()
	{
		return (string) $this->activeEventId;
	}


}