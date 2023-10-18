<?php

namespace Eventjuicer\Services;

use Closure;
use Illuminate\Support\Collection;
use Eventjuicer\Repositories\EloquentTicketRepository;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\CompanyRepository;
use Illuminate\Contracts\Cache\Repository as Cache;
use Eventjuicer\Models\Event;
use Eventjuicer\Models\PromoPrize;
use Eventjuicer\Crud\Visitors\GetVisitorsForPeriod;
use Eventjuicer\Services\ApiUser;


class PartnerPerformanceLocal {
	

	protected $repo, $participants, $companies, $role, $cache, $gaView, $analytics;
	
	protected $statsDefault = [
		"sessions" => 0, 
		"conversions" => 0, 
		"position" => 0
	];

	
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
	

   	public function getExhibitorRankingPosition($active_event_id){

		$ga = $this->getAnalyticsForSource($active_event_id);

		$participants = $this->role->get($active_event_id, "exhibitor", ["company.data"]);

		//filter...we only need exhbitors with company assigned!

		$participants = $participants->filter(function($item){
			return $item->company;
		});

		//filter...we must only have unique companies....

		$participants = $participants->unique("company_id");

		//enrich with GA data....
		$mapped = $this->mergeExhibitorsBySlug($participants, $ga);

		$sorted = $mapped->sortByDesc("company.stats.sessions")->values();

		$position = 1;

		return $sorted->map(function($exh) use (&$position) {

			$stats = $exh->company->stats;

			$stats["position"] = $stats["sessions"] > 0 ? $position : 0;
			
			$prizes = $this->getPrizes($exh->group_id); 

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

			$exh->company->stats = $stats;
			
			//update position!
			$position++;

			return $exh;

		});

	}



	public function mergeExhibitorsBySlug(Collection $participants, Collection $analytics) {

		
		$analytics = $analytics->keyBy("company_id");

		$participants->map(function($row) use ($analytics){

			if($row->company_id && $row->company){

				$cd_lookup = $row->company->data->where("name", "ranking_tweak");

				$tweak_value = $cd_lookup->count() ? intval( $cd_lookup->first()->value ) : 0;

				if($analytics->has($row->company->id)){
					$stats = $analytics->get($row->company->id)->toArray();
				}else{
					$stats = $this->getDefaultStats();
				}


				$tweakedSessions = $stats["sessions"] + $tweak_value;

				$stats["sessions"] = $tweakedSessions > 0 ? $tweakedSessions : 0;

				$row->company->stats = $stats;
			
			}

			return $row;
			
		});

		return $participants;

   	}


 
   

	private function getAnalyticsForSource($eventId) : Collection{
			
		$rankingRepo = app(GetVisitorsForPeriod::class);
		$rankingRepo->setEventId($eventId);
		$rankingRepo->setStartDate($this->startDate);
		$rankingRepo->setEndDate($this->endDate);
		$data = $rankingRepo->get();


		return $data;

		/**
		 * 
		 return [

					"slug" => $pageRow[0],
					'sessions' => (int) $pageRow[1],

				];
			});
		 */
	}





	private function getParticipantsWithRole($role, int $eventId, array $withRels = []) : Collection
	{


		$query = function() use ($role, $eventId, $withRels)
		{
			return $this->role->get($eventId, $role, $withRels);
		};

		return env("USE_CACHE", true) ? $this->cache->remember("all_" . $role . "_" . $eventId, 10, $query) : $query();
    
	}



}