<?php

namespace Eventjuicer\Services;

use Illuminate\Http\Request;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\CompanyRepository;
use Eventjuicer\Models\Group;
use Eventjuicer\Models\Event;

use Eventjuicer\Contracts\Email\Templated;

use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\ColumnGreaterThanZero;
use Eventjuicer\Repositories\Criteria\RelHasNonZeroValue;

use Eventjuicer\ValueObjects\EmailAddress;


class ApiUser {
	
	protected $request, $json, $participants, $token, $user, $company, $representative;
	
	static $tokenRegExp = "/[a-z0-9]{32,40}/";

	function __construct(

		Request $request, 
		ParticipantRepository $participants
		)
	{
		$this->request = $request;
		$this->participants = $participants;
	

		$token = $request->header("x-token", 
			$request->input("x-token", null)
		);

		$this->setToken($token);

	}

	public function getToken()
	{
		return $this->token;
	}

	public function switchToParent()
	{
		//is it sub account?
		if($this->parent_id)
		{
			$this->representative = clone $this;

			$this->setToken($this->parent->token);

			return true;
		}

		return false;
		
	}

	public function realUser()
	{

		return is_object($this->representative) ? $this->representative->user()->fresh() : $this->user();
	}

	public function setToken($token)
	{

		$this->token = $token;

		if($this->tokenIsValid())
		{
			$this->setUser();
		}
	}

	public function user()
	{
		return $this->user;
	}


	public function company()
	{
		return $this->company;
	}


	public function __get($attr)
	{
		return $this->user ? $this->user->{$attr} : 0; 
	}

	
	public function canAccess($obj)
	{

		if(isset($obj->company_id) && $obj->company_id > 0)
		{
			return $this->company_id == $obj->company_id;
		}

		if($obj->group_id > 0)
		{
			return $this->group_id == $obj->group_id;
		}

		if(isset($obj->event_id) && $obj->event_id > 0)
		{

			return $this->group_id == Event::find($obj->event_id)->group_id;
		}


		return false;
		
	}


	public function assignToCompany($company_id)
	{

		if(!$company_id || $this->parent_id)
		{
			throw new \Exception("cannot assign a company...");
		}


		$this->participants->update(compact("company_id"), $this->user->id);

		//we have to refresh our data, right????
		$this->setUser();

	}

	//scan or participants... check unique event_ids...!

	public function accessibleEvents()
	{

	}

	public function slug()
	{
		return str_slug((new EmailAddress($this->user->email))->domain());

	}


	public function isValid()
	{
		return ( $this->user && $this->user->group_id && $this->user->group_id);
	}

	public function log()
	{

	}

	public function activeEventId($strict = false)
	{	

		if($this->company)
		{
			return (int) Group::findOrFail( $this->company->group_id )->active_event_id;
		}

		if($strict)
		{
			return false;
		}

		return (int) Group::findOrFail( $this->group_id )->active_event_id;
	}

	protected function setUser()
	{

		if($this->tokenIsValid())
		{
			$this->user = $this->participants->findBy("token", $this->token);

			$this->setCompany();
		}

	}


	protected function setCompany()
	{
		$this->company = null;

		if($this->user)
		{
			if($this->user->parent_id && $this->user->parent->company_id)
			{
				$this->company = $this->user->parent->company;

				return true;
			} 

			if($this->user->company_id)
			{
				$this->company = $this->user->company;

				return true;
			}
		}
		
	}
	
	public function tokenIsValid()
	{

		return $this->token && preg_match(self::$tokenRegExp, $this->token);
      
	}


}