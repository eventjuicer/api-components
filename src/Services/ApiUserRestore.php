<?php

namespace Eventjuicer\Services;

use Illuminate\Http\Request;
use Eventjuicer\Repositories\ParticipantRepository;



use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\ColumnGreaterThanZero;
use Eventjuicer\Repositories\Criteria\RelHasNonZeroValue;

class ApiUserRestore {
	
	protected $request, $json, $participants;
	

	function __construct(

		Request $request, 
		ParticipantRepository $participants
		)
	{
		$this->request = $request;
		$this->participants = $participants;
	
		$this->json = json_decode($request->getContent(), true);

	}



	public function postData($key = null)
	{
		return $key ? array_get($this->json, $key, null) : $this->json;
	}

	


	public function authenticate()
	{
		$email = $this->postData("email");
		$token = $this->postData("token");
		$password = $this->postData("password");


		$subaccounts = $this->findSubaccountUser("email", $email);
		$master = $this->findCompanyUser("email", $email);

		dd($subaccounts->toArray());

		$merged = $subaccounts->merge($master);

		

      	//dd($merged->toArray());

     // 	dd($subaccounts->merge($other)->toArray());
		



		if($email && !$password)
		{
			//we must search for all users 

		

		}


		return null;

	}

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


	


}