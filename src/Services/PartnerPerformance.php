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

use Illuminate\Contracts\Cache\Repository as Cache;
use Eventjuicer\Models\Event;
use Eventjuicer\Models\PromoPrize;

use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;

use Eventjuicer\Services\ApiUser;

use Carbon\Carbon;

class PartnerPerformance {
	

	protected $repo, $participants, $companies, $role, $cache, $gaView, $analytics;
	
	protected $statsDefault = ["sessions" => 0, "conversions" => 0, "position" => 0];

	
	protected $startDate;
	protected $endDate;
	protected $prefix = "yy14dcs4_"; //"th3rCMiM_";

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
		return PromoPrize::where("group_id", $groupId)->where("disabled", 0)->get();
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

	public function merge(Collection $participants, 
						Collection $analytics, 
						string $glue = "stats", 
						string $mergeBy = "company_id")
	{

		$analytics = $analytics->keyBy("id");

		$participants->map(function($row) use ($analytics, $glue, $mergeBy)
		{

			$cd_lookup = $row->data->where("name", "ranking_tweak");

			$tweak_value = $cd_lookup->count() ? intval( $cd_lookup->first()->value ) : 0;

			if(!is_null($row))
			{

				$stats = $analytics->get($row->$mergeBy, $this->getDefaultStats() );

				$tweakedSessions = $stats["sessions"] + $tweak_value;

				$stats["sessions"] = $tweakedSessions? $tweakedSessions : 0;

				$row->{$glue} = $stats;
			}


		});

		return $participants;

   	}

   	public function mergeExhibitors(Collection $participants, 
						Collection $analytics, 
						string $glue = "stats", 
						string $mergeBy = "company_id")
	{

		$analytics = $analytics->keyBy("id");

		$participants->map(function($row) use ($analytics, $glue, $mergeBy)
		{

			if($row->company_id && $row->company){
				
				$cd_lookup = $row->company->data->where("name", "ranking_tweak");

				$tweak_value = $cd_lookup->count() ? intval( $cd_lookup->first()->value ) : 0;
	
				$stats = $analytics->get($row->$mergeBy, $this->getDefaultStats() );

				$tweakedSessions = $stats["sessions"] + $tweak_value;

				$stats["sessions"] = $tweakedSessions > 0 ? $tweakedSessions : 0;

				$row->company->{$glue} = $stats;
			
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
		$mapped = $this->mergeExhibitors($participants, $ga);

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



   	public function getStatsForCompanies($eventId, $period = 90)
	{	

		$data = $this->getParticipantsWithRole("exhibitor", $eventId, ['company.data']);

		$companies = $data->pluck("company")->unique();

		//in case some company was not assigned!!!

		$companies = $companies->filter(function($value, $key){
			return $value != null;
		});

		$ga = $this->getAnalyticsForSource($this->prefix, $period);


		//we used glue company_id when we matched with participants.. => plucking companies!
		return $this->merge($companies, $ga, "stats", "id");

	}




	//IT SHOULD make use of getExhibitorRankingPosition
	//used by api user limits

	public function getCompanyRankingPosition(ApiUser $apiUser)
	{

		$stats = $this->getStatsForCompanies( $apiUser->activeEventId() );

		$companies = $stats->sortByDesc("stats.sessions")->values();

		$position = 0;

		$stats = ["position" => 0, "points" => 0, "sessions" => 0];

		foreach($companies AS $company)
		{	
			$position++;

			if( $company->id == $apiUser->company()->id )
			{
				$stats = [ 
					"position" 	=> $position, 
					"points" 	=> array_get($company->stats, "conversions"),
					"sessions" 	=> array_get($company->stats, "sessions")
				];
			}
		}

		return $stats;
	}


	/*
		

	$startDate = Carbon::now()->subYear();
	$endDate = Carbon::now();

	Period::create($startDate, $endDate);

	*/


/**
 * 
 * 
 
 https://developers.google.com/analytics/devguides/reporting/core/v3/reference?hl=en#filters
 https://analytics.google.com/analytics/web/?authuser=1#/report/trafficsources-campaigns/a34532684w62106751p63645499/_u.date00=20220324&_u.date01=20220324&_r.drilldown=analytics.campaign:promoninja&explorer-graphOptions.selected=analytics.nthDay/
 */

	public function getAnalyticsForSource($search="", $period = 90) : Collection
	{

		if(!$this->analytics)
		{
			throw new \Exception("No analytics VIEW set.");
		}


		$query = function() use ($search, $period)
        {

        	//$dt = Period::days($period);

        	//ebe5
        	$dt = Period::create(
        		Carbon::createFromDate(2022, 03, 20), 
        		Carbon::create(2022, 05, 04, 23, 59, 59)
        	);

			$response = $this->analytics->performQuery(
				$dt, 
				"ga:sessions",  
				[
				// 'metrics' => 'ga:sessions,ga:sessionDuration',
				// 'dimensions' 	=> 'ga:pagePath',
				'dimensions' 	=> 'ga:source',
				'sort' 			=> '-ga:sessions',
			    'filters'		=> 'ga:source=@' . $search,
				// 'filters'		=> 'ga:pagePath=~exhibitors/[a-z0-9\-]+$',
				"max-results" => 500
				]
			);

			// dd($response["rows"]);

			return collect($response['rows'] ?? [])->map(function (array $pageRow, $position) use ($search) {
				return [
					'id' 			=> (int) str_replace($search, "", $pageRow[0]),
					'sessions' 		=> (int) $pageRow[1],
					'conversions' 	=> 0,
					// "duration"		=> $pageRow[2]
				];
			});
		};

		return env("USE_CACHE", false) ? 
			$this->cache->remember($this->gaView . $search . "new", 10, $query) : 
			$query();

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