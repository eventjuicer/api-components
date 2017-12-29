<?php

namespace Eventjuicer\Services;

use Illuminate\Http\Request;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\CompanyRepository;
use Eventjuicer\Models\Group;


class ApiUser {
	
	protected $request, $participants, $token;
	
	static $tokenRegExp = "/[a-z0-9]{32,40}/";

	function __construct(Request $request, ParticipantRepository $participants)
	{
		$this->request = $request;
		$this->participants = $participants;
	

		$this->token = $request->header("x-token", 
			$request->input("x-token", null)
		);
	}


	function setToken($token)
	{
		if($this->validateToken($token))
		{
			$this->token = $token;
		}
	}


	function autoAssignCompany()
	{



	}

	public function validateToken($token)
	{

		return preg_match(self::$tokenRegExp, $token);
      
	}

	public function canAccess($obj)
	{
		return $this->groupId() == $obj->group_id;
	}

	//scan or participants... check unique event_ids...!

	public function accessibleEvents()
	{

	}


	public function groupId()
	{
		$user = $this->user();

		if( !$user || !$user->group_id )
		{
			throw new \Exception("Company config error!");
		}

		return $user->group_id;
	}


	public function activeEventId()
	{
		$groupId = $this->groupId();
		
		$activeEventId = (int) Group::findOrFail($groupId)->active_event_id;

		if( !$activeEventId )
        {
          throw new \Exception("Group config error");
        } 

        return $activeEventId;
	}

	public function companyId()
	{
		$user = $this->user();

		return $user ? $user->company_id : 0;
	}

	public function user()
	{
		return $this->token ? $this->participants->findBy("token", $this->token) : null;
	}


}