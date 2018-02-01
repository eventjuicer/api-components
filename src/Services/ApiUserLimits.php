<?php
namespace Eventjuicer\Services;

use Eventjuicer\Services\PartnerPerformance;
use Eventjuicer\Services\ApiUser;
use Eventjuicer\Repositories\CompanyRepository;
use Bosnadev\Repositories\Eloquent\Repository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;

class ApiUserLimits {

	protected $user, $performance, $companies;

	protected $stats;

	function __construct(
		ApiUser $user, 
		PartnerPerformance $performance, 
		CompanyRepository $companies
	)
	{

		$this->user 		= $user;
		$this->performance 	= $performance;
		$this->companies 	= $companies;


		$this->performance->setView(112949308);
		
		//PL 63645499

	}


	public function stats()
	{

		if(!is_null($this->stats))
		{
			return $this->stats;
		}

		return $this->stats = $this->companies->updateStatsIfNeeded($this->user->company()->id, function()
		{
			 return $this->performance->getCompanyRankingPosition($this->user);
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

		$howmany = 0;

		if(isset($params[0]) && $params[0] instanceof Repository)
		{
			$howmany = $params[0]->pushCriteria(
				new BelongsToCompany($this->user->company()->id)
			)->all()->count();
		}

		switch($name)
		{
			case "meetup":

				$base = 5;

				return $base + intval($this->points() / 5) - $howmany;

			break;
		}

		return $results;

	}



}