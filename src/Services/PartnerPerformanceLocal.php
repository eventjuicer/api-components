<?php

namespace Eventjuicer\Services;

use Closure;
use Illuminate\Support\Collection;

use Spatie\Analytics\Period;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\AnalyticsClientFactory;


use Eventjuicer\Repositories\EloquentTicketRepository;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\CompanyRepository;
use Eventjuicer\Crud\Visitors\GetVisitorsForPeriod;

use Illuminate\Contracts\Cache\Repository as Cache;
use Eventjuicer\Models\Event;
use Eventjuicer\Models\Company;

use Eventjuicer\Models\PromoPrize;


use Carbon\Carbon;

class PartnerPerformanceLocal {
	

	protected $repo, $participants, $companies, $role, $cache, $gaView, $analytics;
	
	protected $statsDefault = ["sessions" => 0, "conversions" => 0, "position" => 0];	
	protected $startDate;
	protected $endDate;
	static protected $prizesCache = [];

	function __construct(
			EloquentTicketRepository $repo, 
			ParticipantRepository $participants,
			CompanyRepository $companies,
			GetByRole $role,
			Cache $cache)
	{
	
		$this->repo = $repo;
		$this->participants = $participants;
		$this->companies = $companies;
		$this->role = $role;
		$this->cache = $cache;

	}

	public function getPrizes($groupId = 0){


		if(!isset(static::$prizesCache[$groupId])){
			static::$prizesCache[$groupId] = PromoPrize::where("group_id", $groupId)->where("disabled", 0)->get();
		}

		return static::$prizesCache[$groupId] ;

		// return PromoPrize::where("group_id", $groupId)->where("disabled", 0)->get();
	}

	public function setStartDate(DateTime $startDate){
		$this->startDate = $startDate;
	}

	public function setEndDate(DateTime $endDate){
		$this->endDate = $endDate;
	}

	public function getDefaultStats(){
		return $this->statsDefault;
	}


	private function getLocalRanking($eventId){
		
		$rankingRepo = app(GetVisitorsForPeriod::class);
        $rankingRepo->setEventId($eventId);
        $rankingRepo->setStartDate($this->startDate);
        $rankingRepo->setEndDate($this->endDate);
		$data = $rankingRepo->get();
		$arr = $data->keyBy("company_id");

        return $arr;
	}


   	public function getExhibitorRankingPosition($active_event_id){

		$exhibitors = $this->getParticipantsWithRole(
            "exhibitor", 
            $active_event_id, 
            ['company.data']
        );

		$merged = $this->mergeExhibitorsWithRanking(
			$exhibitors, 
			$this->getLocalRanking($active_event_id)
		);

		$sorted = $merged->sortByDesc("company.stats.sessions")->values();

		$position = 1;

		return $sorted->map(function($exhibitor) use (&$position) {
			
			$stats = $exhibitor->company->stats;

			$stats["position"] = $stats["sessions"] > 0 ? $position : 0;
			
			$prizes = $this->getPrizes($exhibitor->group_id); 

			//check what prizes ...

			$stats["prizes"] = collect($prizes)->filter(function($prize) use ($stats) {
				
				if($stats["position"] < $prize["min"] || $stats["position"] > $prize["max"]){
					return false;
				}

				if($stats["sessions"] < $prize["level"]){
					return false;
				}

				return true;

			})->map(function($item){ return $item["name"]; })->values();

			$exhibitor->company->stats = $stats;
			
			//update position!
			$position++;

			return $exhibitor;

		});

	}

   	// public function getStatsForCompanies($eventId)
	// {	

	// 	$companies = $this->getParticipantsWithRole(
    //         "exhibitor", 
    //         $eventId, 
    //         ['company.data']
    //     );

	// 	$companies = $companies->pluck("company");

	// 	return $this->mergeCompaniesWithRanking($companies, $this->getLocalRanking($eventId));

	// }


    // private function mergeCompaniesWithRanking(Collection $companies, array $ranking){

	// 	$companies->map(function($company) use ($ranking){
	// 		if(!is_null($company)){
	// 			$company->stats = $this->enhanceStats($company, $ranking);
	// 		}
	// 		return $company;
	// 	});
	// 	return $companies;
   	// }


	public function mergeExhibitorsWithRanking(Collection $exhibitors, Collection $ranking) {

	
		$exhibitors->map(function($exh) use ($ranking){
			if($exh->company_id && $exh->company){
				$exh->company->stats = $this->enhanceStats($exh->company, $ranking);
			}
			return $exh;
		});
		return $exhibitors;
   	}

	private function enhanceStats(Company $company, Collection $ranking){

		$cd_lookup = $company->data->where("name", "ranking_tweak");
		$tweak_value = $cd_lookup->count() ? intval( $cd_lookup->first()->value ) : 0;
		/**
		 * we must cast to array
		 */

		if($ranking->has($company->id)){
			$stats = $ranking->get($company->id)->toArray();
		}else{
			$stats = $this->getDefaultStats();
		}

		$tweakedSessions = $stats["sessions"] + $tweak_value;
		$stats["sessions"] = $tweakedSessions > 0 ? $tweakedSessions : 0;
		return $stats;
	}


	private function getParticipantsWithRole($role, int $eventId, array $withRels = []) : Collection
	{

		$query = function() use ($role, $eventId, $withRels)
		{
			$data = $this->role->get($eventId, $role, $withRels);
			$exh = $data->filter(function($item){
				return $item->company;
			});
			//filter...we must only have unique companies....
			$exh = $exh->unique("company_id");
			return $exh;
		};

		return env("USE_CACHE", true) ? $this->cache->remember("all_" . $role . "_" . $eventId, 10, $query) : $query();
    
	}



}