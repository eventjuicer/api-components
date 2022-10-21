<?php
namespace Eventjuicer\Services;

use Bosnadev\Repositories\Eloquent\Repository;
use Eventjuicer\Services\PartnerPerformance;
use Eventjuicer\Repositories\CompanyRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Services\Company\GetActiveEventId;
use Eventjuicer\Services\Company\GetCompanyDataValue;

class ApiUserLimits {

	protected $company, $performance, $companies;

	protected $stats = null;

	function __construct(PartnerPerformance $performance, CompanyRepository $companies){

		$this->performance 	= $performance;
		$this->companies 	= $companies;

		$user = app("request")->user();

		if($user && $user->company){
			
			$this->company = $user->company;

			if($this->company->group_id == 1) {

				$this->performance->setView(63645499);
	
			} else {
	
				//BERLIN
				$this->performance->setView(112949308);
			}

			
		}
		
	}

	public function stats(){

		if(!is_null($this->stats))
		{
			return $this->stats;
		}

		return $this->stats = $this->companies->updateStatsIfNeeded($this->company->id, function()
		{
			return $this->performance->getExhibitorRankingPosition(
				(string) new GetActiveEventId($this->company)
			 ); 
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


	public function __call($name, $params){	

		if(!$this->company){
			return 0;
		}

		$this->stats(); //refresh stats if old...

		$cd = new GetCompanyDataValue($this->company);

		$i_tweak = intval(  $cd->get("invitations_tweak")  );
		$v_tweak = intval(  $cd->get("vip_tweak") );

		$name = str_singular($name);

		$base = 0;
		$earned = 0;
		$used = 0;
		$remaining = 0;

		if(isset($params[0]) && $params[0] instanceof Repository)
		{
			$params[0]->pushCriteria(
				new BelongsToCompany( (int) $this->company->id )
			);

			$params[0]->pushCriteria(
				new BelongsToEvent( (string) new GetActiveEventId($this->company)  )
			);

			if($name === "meetup"){
				$params[0]->pushCriteria(
					new FlagEquals("direction", "C2P")
				);
	
			}


			$used = $params[0]->all()->count();

		}

		switch($name){


			case "meetup":

				if($this->company->organizer_id > 1){
					$base = 30 + $i_tweak;
				}else{
					$base = 10 + $i_tweak;
				}
				
				if($this->points() > 19){
					$earned = 50;
				}
				
				$earned = $earned + intval($this->points() / 50) * 50;
		
				if($base + $earned > 200){
					return 200;
				}

			break;


			case "vip":

				$base = 10;
				
				if($this->points() > 19){
					$earned = 5;
				}
				
				$earned = $earned + intval($this->points() / 50) * 5;
		
				if($base + $earned > 25){
					return 25 + $v_tweak;
				}

			break;



			case "scan":

				$base = 10 + $v_tweak;
				
				if($this->points() > 19){
					$earned = 100000;
				}
				
			break;


		}

		$remaining = $base + $earned - $used;

		return $remaining >= 0 ? $remaining : 0;

	}



}