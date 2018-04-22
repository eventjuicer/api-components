<?php 

namespace Eventjuicer\Services;

use Eventjuicer\Models\Host;
use Eventjuicer\Models\Group;


class Resolver {
	

	protected $host;
	protected $organizer_id = 0, $groupId = 0, $activeEventId = 0;


	function __construct(string $host)
	{
		$this->host = strtolower(trim($host));

		$this->resolve();
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

	public function getEventId()
	{
		return $this->activeEventId;
	}

	public function __toString()
	{
		return (string) $this->activeEventId;
	}


}