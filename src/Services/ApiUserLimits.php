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
use Eventjuicer\Services\Company\UpdateCompanyStats;


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


		$updater = new UpdateCompanyStats($this->company);
	
		if(!$updater->statsAreFresh()){
			$ranking = $this->performance->getExhibitorRankingPosition(
				(string) new GetActiveEventId($this->company)
			);

			$updater->updateWithRanking($ranking);
		}

		$this->stats = $updater->getCachedStats();



	}

	public function points()
	{
		return array_get($this->stats(), "sessions", 0);
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


		if($params[0] instanceof Repository)
		{
			$repo = $params[0];
		}else{
			$repo = app($params[0]);
		}


		$repo->pushCriteria(
			new BelongsToCompany( (int) $this->company->id )
		);

		$repo->pushCriteria(
			new BelongsToEvent( (string) new GetActiveEventId($this->company)  )
		);

		if($name === "meetup"){
			$repo->pushCriteria(
				new FlagEquals("direction", "C2P")
			);
		}

		if($name === "vip"){
			$repo->pushCriteria(
				new ColumnGreaterThan("participant_id", 0)
			);
		}


		$used = $repo->all()->count();

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