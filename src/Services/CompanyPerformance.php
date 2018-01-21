<?php

namespace Eventjuicer\Services;

use Closure;
use Illuminate\Support\Collection;



use Analytics;
use Spatie\Analytics\Period;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\AnalyticsClientFactory;


use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\Criteria\WhereIn;
use Eventjuicer\Services\Personalizer;

class CompanyPerformance {
	

	protected $repo, $analytics;
	
	function __construct(ParticipantRepository $repo)
	{
	
		$this->repo = $repo;

		$this->

	}



	function setView($viewId = "")
	{
		$config = config('analytics');
        $client = AnalyticsClientFactory::createForConfig($config);
        return new Analytics($client, $viewId);

	}


	function matchWithCompanies(Collection $data, $role)
	{

		if($role === "company" || $role === "presenter")
		{
			$personalize = "[[cname2]]";
		}
		else
		{
			$personalize = "[[fname]] [[email?obfuscate]]";
		}

	 
     	$this->repo->pushCriteria(new WhereIn("id", $data->pluck("source")->all()));

        $this->repo->with(["fields"]);

        $participants = $this->repo->all()->keyBy("id");

        $data->transform(function($item, $key) use ($participants, $personalize)
        {

        	$id = (int) $item["source"];

        	if(!isset($participants[$id]))
        	{
        		return $item;
        	}

        	$item["name"] = (string) new Personalizer($participants[$id], $personalize);

        	return $item;

        });


   		return $data;

   	}



	function matchWithParticipants(Collection $data, $role)
	{

		if($role === "company" || $role === "presenter")
		{
			$personalize = "[[cname2]]";
		}
		else
		{
			$personalize = "[[fname]] [[email?obfuscate]]";
		}

	 
     	$this->repo->pushCriteria(new WhereIn("id", $data->pluck("source")->all()));

        $this->repo->with(["fields"]);

        $participants = $this->repo->all()->keyBy("id");

        $data->transform(function($item, $key) use ($participants, $personalize)
        {

        	$id = (int) $item["source"];

        	if(!isset($participants[$id]))
        	{
        		return $item;
        	}

        	$item["name"] = (string) new Personalizer($participants[$id], $personalize);

        	return $item;

        });


   		return $data;

   	}
/*

$startDate = Carbon::now()->subYear();
$endDate = Carbon::now();

Period::create($startDate, $endDate);

*/

	public function sourceContains($search="", $period = 90)
	{

		return $this->repo->cached($search, 10, function() use ($search, $period)
        {

			$response = Analytics::performQuery(

				Period::days($period), 

				"ga:sessions",  
				[
				'dimensions' 	=> 'ga:source',
				'sort' 			=> '-ga:sessions',
				'filters'		=> 'ga:source=@' . $search
				]

			);

			return collect($response['rows'] ?? [])->map(function (array $pageRow) use ($search) {
				return [
					'source' 	=> str_replace($search, "", $pageRow[0]),
					'sessions' 	=> $pageRow[1]
				];
			});

		});

	}



}