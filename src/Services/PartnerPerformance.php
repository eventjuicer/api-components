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

use Carbon\Carbon;

class PartnerPerformance {
	

	protected $repo, $participants, $companies, $role, $cache, $gaView, $analytics;
	
	protected $statsDefault = ["sessions" => 0, "conversions" => 0];

	
	protected $startDate;
	protected $endDate;
	protected $prefix = "th4x90iy_"; //"th3rCMiM_";
 

	protected $ebe_prizes = [
  		
		[	
				"name" => "badges", 
				"min" => 1, 
				"max" => 1, 
				"level"=> 500
		],

		[
				"name" => "presentation", 
				"min" => 1, 
				"max" => 3, 
				"level" => 250
		],

		// [		
		// 		"name" => "floor", 
		// 		"min" => 1, 
		// 		"max" => 3, 
		// 		"level" => 50
		// ],

		[		
				"name" => "video_interview", 
				"min" => 1, 
				"max" => 5,
				"level" => 150
		],

		[		
				"name" => "earlybird", 
				"min" => 1, 
				"max" => 30, 
				"level" => 30
		],

		[		
				"name" => "meetups", 
				"min" => 1, 
				"max" => 50, 
				"level" => 50
		],

		[
				"name" => "brand_highlight", 
				"min" => 1, 
				"max" => 4,  
				"level" => 100
		],

		[
				"name" => "leaflets", 
				"min" => 1, 
				"max" => 15,  
				"level" => 30
		],
	
		// [		
		// 		"name" => "scanner", 
		// 		"min" => 1, 
		// 		"max" => 30, 
		// 		"level" => 30
		// ],

		[
				"name" => "rollups", 
				"min" => 1, 
				"max" => 8,  
				"level" => 50
		],

		[		
				"name" => "blog", 
				"min" => 1, 
				"max" => 10,
				"level" => 50
		],

	];

	protected $prizes = [
  		
		// [	
		// 		"name" => "badges", 
		// 		"min" => 1, 
		// 		"max" => 1, 
		// 		"level"=> 200
		// ],
		// [
		// 		"name" => "presentation", 
		// 		"min" => 1, 
		// 		"max" => 2, 
		// 		"level" => 200
		// ],
		// [		
		// 		"name" => "floor", 
		// 		"min" => 1, 
		// 		"max" => 3, 
		// 		"level" => 50
		// ],
		// [
		// 		"name" => "leaflets", 
		// 		"min" => 1, 
		// 		"max" => 10,  
		// 		"level" => 20
		// ],
	
	
		[		
				"name" => "video_interview", 
				"min" => 1, 
				"max" => 5,
				"level" => 50
		],

		[		
				"name" => "earlybird", 
				"min" => 1, 
				"max" => 30, 
				"level" => 30
		],

		[		
				"name" => "meetups", 
				"min" => 1, 
				"max" => 50, 
				"level" => 20
		],

		[
				"name" => "brand_highlight", 
				"min" => 1, 
				"max" => 6,  
				"level" => 40
		],

	
		// [		
		// 		"name" => "scanner", 
		// 		"min" => 1, 
		// 		"max" => 50, 
		// 		"level" => 10
		// ],

		[
				"name" => "rollups", 
				"min" => 1, 
				"max" => 8,  
				"level" => 20
		],

		// [		
		// 		"name" => "blog", 
		// 		"min" => 1, 
		// 		"max" => 10,
		// 		"level" => 20
		// ],

	];

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
		return $groupId > 1 ? $this->ebe_prizes : $this->prizes;
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

				$stats = $analytics->get($row->$mergeBy, $this->statsDefault);

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
	
				$stats = $analytics->get($row->$mergeBy, $this->statsDefault);

				$tweakedSessions = $stats["sessions"] + $tweak_value;

				$stats["sessions"] = $tweakedSessions > 0 ? $tweakedSessions : 0;

				$row->company->{$glue} = $stats;
			
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

		$ga = $this->getAnalyticsForSource($this->prefix, $period);


		//we used glue company_id when we matched with participants.. => plucking companies!
		return $this->merge($companies, $ga, "stats", "id");

	}


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
        		Carbon::createFromDate(2020, 01, 15), 
        		Carbon::create(2020, 02, 04, 23, 59, 59)
        	);

			$response = $this->analytics->performQuery(
				$dt, 
				"ga:sessions",  
				[
				'dimensions' 	=> 'ga:source',
				'sort' 			=> '-ga:sessions',
				'filters'		=> 'ga:source=@' . $search// . '&ga:sessionDuration>'
				]
			);

			$sorted = collect($response['rows'] ?? [])->map(function (array $pageRow, $position) use ($search) {
				return [
					'id' 			=> (int) str_replace($search, "", $pageRow[0]),
					'sessions' 		=> (int) $pageRow[1],
					'conversions' 	=> 0
				];
			})->sortByDesc("sessions");

			$position = 1;

			return $sorted->transform(function($item) use (&$position) {

				$item["position"] = $position;

				$position++;

				return $item;

			})->values();
		};

		return $query();

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