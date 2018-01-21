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


use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;

use Eventjuicer\Services\ApiUser;

class PartnerPerformance {
	

	protected $repo, $participants, $comanies, $cache, $gaView, $analytics;
	
	protected $statsDefault = ["sessions" => 0, "conversions" => 0];

	function __construct(
			EloquentTicketRepository $repo, 
			ParticipantRepository $participants,
			CompanyRepository $companies,
			Cache $cache)
	{
	
		$this->repo = $repo;
		$this->participants = $participants;
		$this->companies = $companies;
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

			$row->{$glue} = $analytics->get($row->$mergeBy, $this->statsDefault);

		});

		return $participants;

   	}


   	public function getStatsForCompanies($eventId)
	{	


		$data = $this->getParticipantsWithRole(["exhibitor"], $eventId);

		//company_
		$ga = $this->getAnalyticsForSource("company_");

		//company_id
		return $this->merge($data, $ga, "stats", "company_id");


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

			$response = $this->analytics->performQuery(

				Period::days($period), 

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

	private function getParticipantsWithRole(array $roles, int $scopeValue, string $scope = "event" ) : Collection
	{

		$query = function() use ($roles, $scopeValue, $scope){

			$this->repo->with([
            "participantsNotCancelled.company"

        ]);

        foreach($roles as $role)
        {
        	$this->repo->pushCriteria(new ColumnMatches("role", $role, false));

        }

        switch($scope)
        {
            case "event":
                $this->repo->pushCriteria(new BelongsToEvent($scopeValue));
            break;

            case "group":
                $this->repo->pushCriteria(new BelongsToGroup($scopeValue));
            break;

        }

        return $this->repo->all()->pluck("participantsNotCancelled")->collapse();

		};

        return env("USE_CACHE", true) ? 
			$this->cache->remember("PP_getParticipants_".$scopeValue, 10, $query) : 
			$query();
    
	}



}