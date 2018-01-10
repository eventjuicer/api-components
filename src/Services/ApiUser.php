<?php

namespace Eventjuicer\Services;

use Illuminate\Http\Request;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\CompanyRepository;
use Eventjuicer\Models\Group;
use Eventjuicer\Contracts\Email\Templated;

use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\ColumnGreaterThanZero;
use Eventjuicer\Repositories\Criteria\RelHasNonZeroValue;

class ApiUser {
	
	protected $request, $json, $participants, $token, $user, $company;
	
	static $tokenRegExp = "/[a-z0-9]{32,40}/";

	function __construct(

		Request $request, 
		ParticipantRepository $participants
		)
	{
		$this->request = $request;
		$this->participants = $participants;
	

		$this->token = $request->header("x-token", 
			$request->input("x-token", null)
		);

		$this->setUser();

		$this->json = json_decode($request->getContent(), true);

	}



	public function postData($key = null)
	{
		return $key ? array_get($this->json, $key, null) : $this->json;
	}



	/*
	*
	* 
	*/


	protected function findSubaccountUser($findBy, $value)
	{
		//handle subaccounts....

		$this->participants->resetCriteria();

		$this->participants->with(["parent.company"]);

		$this->participants->pushCriteria(new ColumnGreaterThanZero("parent_id"));

		$this->participants->pushCriteria(new RelHasNonZeroValue("parent", "company_id"));

        $this->participants->pushCriteria(new ColumnMatches($findBy, $value));
        
        return $this->participants->all();
	}

	protected function findCompanyUser($findBy, $value)
	{
		$this->participants->resetCriteria();

		$this->participants->with(["company"]);

		$this->participants->pushCriteria(new ColumnGreaterThanZero("company_id"));

      	$this->participants->pushCriteria(new ColumnMatches($findBy, $value));

      	return $this->participants->all();

	}



	public function authenticate()
	{
		$email = $this->postData("email");
		$token = $this->postData("token");
		$password = $this->postData("password");


		$subaccounts = $this->findSubaccountUser("email", $email);
		$master = $this->findCompanyUser("email", $email);

		//dd($subaccounts->toArray());

		$merged = $subaccounts->merge($master);

		

      	//dd($merged->toArray());

     // 	dd($subaccounts->merge($other)->toArray());
		



		if($email && !$password)
		{
			//we must search for all users 

		

		}


		return null;

	}



	
	public function setToken($token)
	{
		if($this->validateToken($token))
		{
			$this->token = $token;
			$this->setUser();
		}
	}

	public function user()
	{
		return $this->user;
	}

	public function __get($attr)
	{
		return $this->user ? $this->user->{$attr} : 0; 
	}

	function autoAssignCompany()
	{



	}

	
	public function canAccess($obj)
	{

		if(isset($obj->company_id) && $obj->company_id > 0)
		{
			return $this->company_id == $obj->company_id;
		}

		return $this->group_id == $obj->group_id;
		
	}


	//scan or participants... check unique event_ids...!

	public function accessibleEvents()
	{

	}


	public function isValid()
	{
		return ( $this->user && $this->user->group_id && $this->user->group_id);
	}

	public function activeEventId()
	{	
		return (int) Group::findOrFail($this->group_id )->active_event_id;
	}

	protected function setUser()
	{

		if($this->token && $this->validateToken($this->token))
		{
			$query = $this->participants->findBy("token", $this->token);

			//conditions???

			$this->user = $query;
		}

	}


	protected function setCompany()
	{
		if($this->user)
		{
			//resolve from parent?

			if(!$this->user->company_id)
			{
				if($this->user->parent_id)
				{
					if($this->user->parent->company)
					{
						$this->company = $this->user->parent->company;
					}
				}
				
			}
			else
			{
				$this->company = $this->user->company;
			}
		}
		
	}
	
	protected function validateToken($token)
	{

		return preg_match(self::$tokenRegExp, $token);
      
	}


}