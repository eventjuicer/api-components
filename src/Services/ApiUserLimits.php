<?php
namespace Eventjuicer\Services;

use Eventjuicer\Services\PartnerPerformance;
use Eventjuicer\Services\ApiUser;
use Eventjuicer\Repositories\CompanyRepository;
use Bosnadev\Repositories\Eloquent\Repository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;

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


		if($this->user->company()->group_id == 1) {

			$this->performance->setView(63645499);

		} else {

			//BERLIN
			$this->performance->setView(112949308);
		}

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

		$tweak = intval( $this->user->setting("invitations_tweak") );

		$name = str_singular($name);

		$base = 0;
		$earned = 0;
		$used = 0;
		$remaining = 0;

		if(isset($params[0]) && $params[0] instanceof Repository)
		{
			$params[0]->pushCriteria(
				new BelongsToCompany($this->user->company()->id)
			);

			$params[0]->pushCriteria(
				new BelongsToEvent($this->user->activeEventId() )
			);

			$used = $params[0]->all()->count();
		}

		switch($name){

			case "meetup":

				$base = 10 + $tweak;

				// if($this->user->company()->id == 1309 || $this->user->company()->id == 1054){
				// 	$base = 500;
				// }

				//TEH!
				if($this->points() > 19){
					$earned = $earned + 50;
				}

				//liczmy ile mamy "50" w naszych punktach w przyblizeniu do liczby całkowitej
				
				//EBE
				$howMany50 = intval($this->points() / 50);

				//za kazde "50" dajemy 50.... 
				$earned = $howMany50 * 50;
				

				//capping
				if($base + $earned > 200){
					$earned = 200 - $base;
				}


			break;
		}

		$remaining = $base + $earned - $used;

		return $remaining >= 0 ? $remaining : 0;

	}



}