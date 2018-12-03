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

use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;

use Eventjuicer\Services\ApiUser;

class PartnerPerformance {
	

	protected $repo, $participants, $companies, $role, $cache, $gaView, $analytics;
	
	protected $statsDefault = ["sessions" => 0, "conversions" => 0];

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



	public function setView($viewId = "")
	{
		$config = config('analytics');

        $client = AnalyticsClientFactory::createForConfig($config);

        $this->gaView = $viewId;
       	
       	$this->analytics = new Analytics($client, $this->gaView);

	}


	private function merge(Collection $participants, 
						Collection $analytics, 
						string $glue = "stats", 
						string $mergeBy = "company_id")
	{

		$analytics = $analytics->keyBy("id");

		$participants->map(function($row) use ($analytics, $glue, $mergeBy)
		{

			if(!is_null($row))
			{
				$row->{$glue} = $analytics->get($row->$mergeBy, $this->statsDefault);

			}


		});

		return $participants;

   	}


   	public function getStatsForCompanies($eventId, $period = 90)
	{	

		$data = $this->getParticipantsWithRole("exhibitor", $eventId, ['company.data']);

		$companies = $data->pluck("company")->unique();

		//in case some company was not assigned!!!

		$companies = $companies->filter(function($value, $key){
			return $value != null;
		});

		$ga = $this->getAnalyticsForSource("th3rCMiM_", $period);

		//we used glue company_id when we matched with participants.. => plucking companies!
		return $this->merge($companies, $ga, "stats", "id");

	}


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
					"points" 	=> array_get($company->stats, "sessions"),
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

	private function getAnalyticsForSource($search="", $period = 90) : Collection
	{

		if(!$this->analytics)
		{
			throw new \Exception("No analytics VIEW set.");
		}


		$query = function() use ($search, $period)
        {

        //	$dt = Period::days($period);

        	$dt = Period::create("2018-10-15", "2018-11-06 23:59:59");

			$response = $this->analytics->performQuery(

				$dt, 

				"ga:sessions",  
				[
				'dimensions' 	=> 'ga:source',
				'sort' 			=> '-ga:sessions',
				'filters'		=> 'ga:source=@' . $search
				]

			);

			return collect($response['rows'] ?? [])->map(function (array $pageRow, $position) use ($search) {
				return [
					'id' 			=> (int) str_replace($search, "", $pageRow[0]),
					'sessions' 		=> (int) $pageRow[1],
					'conversions' 	=> 0
				];
			});

		};


		return env("USE_CACHE", true) ? 
			$this->cache->remember($this->gaView . $search, 10, $query) : 
			$query();

	}


	private function getParticipantsWithRole($role, int $eventId, array $withRels = []) : Collection
	{


		$query = function() use ($role, $eventId, $withRels)
		{
			return $this->role->get($eventId, $role, $withRels);
		};


		return env("USE_CACHE", true) ? $this->cache->remember("xxxxxx" . $role . $eventId, 10, $query) : $query();
    
	}



}