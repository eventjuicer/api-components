<?php

namespace Eventjuicer\Services;


use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Models\Participant;
use Eventjuicer\Repositories\CompanyRepository;

class ApiUserAssign {
	
	protected $user, $repo;
	
	
	function __construct(ApiUser $user)
	{
		$this->user = $user;
		$this->repo = app()->make(CompanyRepository::class);

	}

	public function make($admin = false)
	{


		if(
			$this->user->company_id || 
			($this->user->parent_id && $this->user->parent->company_id))
		{
			return true;
		}


		//REPRESENTATIVE? We only care about master accounts!
		if($this->user->parent_id)
		{

			$this->user->switchToParent();

		}

		$company = $this->findCompany($this->user->group_id, $this->user->slug());

		$hasPaidPurchases = $this->findPaidPurchases($this->user->user());



		if($hasPaidPurchases || $admin)
		{

			if(!$company)
			{			
				$company = $this->createCompany();
			}

			$this->user->assignToCompany($company->id);

			return true;
		}




		return null;

	}


	protected function findPaidPurchases(Model $src)
	{

		return $src->purchases()->where("amount",">", 0)->where("paid", 1)->count();


	}


	protected function createCompany()
	{

		$company = $this->repo->makeModel();

		$this->repo->saveModel(

			[
				"organizer_id" => $this->user->organizer_id,
				"group_id" => $this->user->group_id,
				"assigned_by" => 0,

				"slug" => $this->user->slug(),
				"lang" => "en",
				"name" => $this->user->slug(),

				"password" => "",
				"meetup_limit" => 5
			]

		);

		return $company;
	}


	protected function findCompany($group_id, string $slug) 
	{

		return $this->repo->findWhere([

			["slug", "LIKE", $slug],
			["group_id", (int) $group_id]

		])->first();

	}


}