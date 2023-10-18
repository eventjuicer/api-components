<?php
namespace Eventjuicer\Services;

use Bosnadev\Repositories\Eloquent\Repository;
use Eventjuicer\Services\PartnerPerformanceLocal;
use Eventjuicer\Repositories\CompanyRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\ColumnGreaterThan;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Services\Company\GetActiveEventId;
use Eventjuicer\Services\Company\GetCompanyDataValue;

class ApiUserLimits {

	protected $company, $performance, $companies;

	protected $stats = null;

	function __construct(PartnerPerformanceLocal $performance, CompanyRepository $companies){

		$this->performance 	= $performance;
		$this->companies 	= $companies;

		$user = app("request")->user();

		if($user && $user->company){
			
			$this->company = $user->company;
			
		}
		
	}

	public function stats(){


		if(!is_null($this->stats))
		{
			return $this->stats;
		}


		$ranking = $this->performance->getExhibitorRankingPosition(
			(string) new GetActiveEventId($this->company)
		);

		$exh = $ranking->where("company_id", $this->company->id)->first();


		if($exh && $exh->company){
			$this->stats = $exh->company->stats;
		}else{
			throw new \Exception("not an exh");
		}



		// $this->stats = $this->companies->updateStatsIfNeeded($this->company->id, function()
		// {

		// 	return 1; 
		// });

	

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

			if($name === "vip"){
				$params[0]->pushCriteria(
					new ColumnGreaterThan("participant_id", 0)
				);
			}


			$used = $params[0]->all()->count();

		}

		switch($name){


			case "meetup":

				if($this->company->organizer_id > 1){
					$base = 10 + $i_tweak;
				}else{
					$base = 10 + $i_tweak;
				}
				
				if($this->points() > 19){
					$earned = 50;
				}
				
				$earned = $earned + intval($this->points() / 50) * 50;
		
				if($earned > 200){
					$earned = 190;
				}

			break;


			case "vip":

				$base = 10 + $v_tweak;
				
				if($this->points() > 19){
					$earned = 2;
				}
				
				$earned = $earned + intval($this->points() / 50) * 5;
		
				if($earned > 20){
					$earned = 15;
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