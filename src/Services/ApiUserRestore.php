<?php

namespace Eventjuicer\Services;

use Illuminate\Http\Request;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\CompanyDataRepository;
use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\ColumnMatchesArray;
use Eventjuicer\Repositories\Criteria\ColumnGreaterThanZero;
use Eventjuicer\Repositories\Criteria\RelHasNonZeroValue;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\SortByDesc;
use Eventjuicer\ValueObjects\EmailAddress;
use Eventjuicer\Services\Resolver;


class ApiUserRestore {
	
	protected $request, $json, $participants, $companydata;

	function __construct(

		Request $request, 
		ParticipantRepository $participants,
		CompanyDataRepository $companydata
		)
	{
		
		$this->request = $request;
		$this->participants = $participants;
		$this->companydata = $companydata;

		$this->json = json_decode($request->getContent(), true);

	}



	public function postData($key = null)
	{
		return $key ? array_get($this->json, $key, null) : $this->json;
	}

	
	public function authenticate()
	{
		$company_id = $this->postData("company_id");
		$email = new Email($this->postData("email"));
		$token = $this->postData("token");
		$password = $this->postData("password");
		$project = new Resolver($this->postData("project"));

		if($company_id && $password){
			$this->companydata->pushCriteria(new BelongsToCompany($company_id));
			$this->companydata->pushCriteria(new ColumnMatches("name","password"));
			$row = $this->companydata->all()->first();

			if($row && $row->value === $password){
				return $row->company;
			}

			return false;
		}

		if($email && $password)
		{
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

			return false;
		}

		if($email->isValid()){

			//get domain
			$domain = $email->domain();

			//1. find via exact address
			$this->findByEmail((string) $email, $project->$getGroupId());
			
		}
		

		return null;

	}

	public function findByEmail($email, $group_id){

		$this->participants->makeModel();
		$this->participants->pushCriteria(new ColumnMatches("email", strtolower( trim($email) ) ));
		/** match project */
		$this->participants->pushCriteria(new FlagEquals("group_id", $group_id));
		/** match project */
		$this->participants->pushCriteria(new ColumnGreaterThanZero("company_id"));
		$this->participants->pushCriteria(new SortByDesc());
		return $this->participants->all();

	}


	protected function findSubaccountUser($findBy, string $value)
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