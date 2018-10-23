<?php
namespace Eventjuicer\Services;

use Eventjuicer\Services\PartnerPerformance;
use Eventjuicer\Services\ApiUser;
use Eventjuicer\Repositories\CompanyRepository;
use Bosnadev\Repositories\Eloquent\Repository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;

class ApiUserLimits {

	protected $user, $performance, $companies;

	protected $stats = null;

	function __construct(
		ApiUser $user, 
		PartnerPerformance $performance, 
		CompanyRepository $companies
	)
	{

		$this->user 		= $user;
		$this->performance 	= $performance;
		$this->companies 	= $companies;


		$this->performance->setView(63645499);
		
		//BERLIN 112949308
		//PL 

	}


	public function stats()
	{

		if(!is_null($this->stats))
		{
			return $this->stats;
		}

		return $this->stats = $this->companies->updateStatsIfNeeded($this->user->company()->id, function()
		{
			 return $this->performance->getCompanyRankingPosition($this->user, 21);
		});
	}

	public function points()
	{
		return array_get($this->stats(), "points", 0);
	}

	public function position()
	{
		return array_get($this->stats(), "position", 0);
	}


	public function __call($name, $params)
	{	

		$this->stats();


		$name = str_singular($name);

		$base = 0;
		$earned = 0;
		$used = 0;
		$remaining = 0;

		if(isset($params[0]) && $params[0] instanceof Repository)
		{
			$used = $params[0]->pushCriteria(
				new BelongsToCompany($this->user->company()->id)
			)->all()->count();
		}

		switch($name)
		{
			case "meetup":

				$base = 5;

				if($this->user->company()->id == 1155){
					$base = $base + 30;
				}

				if($this->points() > 19){
					$earned = $earned + 50;
				}

				$earned = $earned + intval($this->points() / 5);


			break;
		}

		$remaining = $base + $earned - $used;

		return $remaining >= 0 ? $remaining : 0;

	}



}