<?php

namespace Eventjuicer\Services;

use Closure;
use Illuminate\Support\Collection;

use Analytics;
use Spatie\Analytics\Period;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\Criteria\WhereIn;
use Eventjuicer\Services\Personalizer;

class PartnerPerformance {
	

	protected $repo;
	

	function __construct(ParticipantRepository $repo)
	{
	
		$this->repo = $repo;
	}


	function matchWithParticipants(Collection $data, $role)
	{

		if($role == "company")
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

	public function sourceContains($search="")
	{

		return $this->repo->cached($search, 30, function() use ($search)
        {

			$response = Analytics::performQuery(

				Period::days(14), 

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