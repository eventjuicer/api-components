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
use Eventjuicer\Models\PromoPrize;


use Carbon\Carbon;

class PartnerPerformanceLocal {
	

	protected $repo, $participants, $companies, $role, $cache, $gaView, $analytics;
	
	protected $statsDefault = ["sessions" => 0, "conversions" => 0, "position" => 0];

	
	protected $startDate;
	protected $endDate;
	protected $prefix = "xx14ycs4_"; //"th3rCMiM_";
	protected $eventName = "promoninja";


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


	public function setView($viewId = "")
	{
		$config = config('analytics');

        $client = AnalyticsClientFactory::createForConfig($config);

        $this->gaView = $viewId;
       	
       	$this->analytics = new Analytics($client, $this->gaView);

	}

	public function setStartDate(DateTime $startDate){
		$this->startDate = $startDate;
	}

	public function setEndDate(DateTime $endDate){
		$this->endDate = $endDate;
	}

	public function setPrefix(string $prefix){
		$this->prefix = $prefix;
	}

	public function getPrefix(){
		return $this->prefix;
	}

	public function getDefaultStats(){
		return $this->statsDefault;
	}

	

   	public function mergeExhibitorsBySlug(Collection $participants, Collection $analytics) {

		$analytics = $analytics->keyBy("slug");

		$participants->map(function($row) use ($analytics){

			if($row->company_id && $row->company){

				$cd_lookup = $row->company->data->where("name", "ranking_tweak");

				$tweak_value = $cd_lookup->count() ? intval( $cd_lookup->first()->value ) : 0;
	
				$stats = $analytics->get( $row->company->slug, $this->getDefaultStats() );

				$tweakedSessions = $stats["sessions"] + $tweak_value;

				$stats["sessions"] = $tweakedSessions > 0 ? $tweakedSessions : 0;

				$row->company->stats = $stats;
			
			}

			return $row;
			
		});

		return $participants;

   	}


   	public function getExhibitorRankingPosition($active_event_id){

		$ga = $this->getAnalyticsForSource($this->getPrefix(), 90);

		$participants = $this->role->get($active_event_id, "exhibitor", ["company.data"]);

		//filter...we only need exhbitors with company assigned!

		$participants = $participants->filter(function($item){
			return $item->company != null;
		});

		//filter...we must only have unique companies....

		$participants = $participants->unique("company_id");

		//enrich with GA data....
		$mapped = $this->mergeExhibitorsBySlug($participants, $ga);

		$sorted = $mapped->sortByDesc("company.stats.sessions")->values();


		$position = 1;

		return $sorted->map(function($exh) use (&$position) {

			
			$stats = isset($exh->company->stats) ? $exh->company->stats: $this->getDefaultStats();
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

    /**
     * 
     * USED by /restricted/ranking ....
     * 
     */

   	public function getStatsForCompanies($eventId, $start = null, $end = null)
	{	

		$data = $this->getParticipantsWithRole(
            "exhibitor", 
            $eventId, 
            ['company.data']
        );
		
        $companies = $data->pluck("company")->unique();

		//in case some company was not assigned!!!
		$companies = $companies->filter(function($value, $key){
			return $value != null;
		});

        $rankingRepo = app(GetVisitorsForPeriod::class);
        $rankingRepo->setEventId($eventId);
        $rankingRepo->setStartDate($start);
        $rankingRepo->setEndDate($end);

        $ranking = $rankingRepo->get();

		return $this->mergeByCompanyId($companies, $ranking);

	}


    private function mergeByCompanyId(Collection $companies, Collection $ranking){

		$_ranking = $ranking->keyBy("company_id");

		$companies->map(function($row) use ($_ranking){

			$cd_lookup = $row->data->where("name", "ranking_tweak");

			$tweak_value = $cd_lookup->count() ? intval( $cd_lookup->first()->value ) : 0;

			if(!is_null($row)){

				$stats = $_ranking->get($row->id, $this->getDefaultStats() );

				$tweakedSessions = $stats["sessions"] + $tweak_value;

				$stats["sessions"] = $tweakedSessions? $tweakedSessions : 0;

				$row->stats = $stats;
			}


		});

		return $companies;

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