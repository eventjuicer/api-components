<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Company;
use Eventjuicer\Repositories\Repository;

use Carbon\Carbon;
use Closure;
use Log;

class CompanyRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return Company::class;
    }


	/** it should return Company  */
	
    public function updateStatsIfNeeded($id, Closure $source)
    {

    	$company = $this->find($id);

    	if(!$company) 
    	{
    		throw new \Exception("Company $id missing...");
    	}

    	if(! is_null($company->stats_updated_at) && Carbon::now()->diffInMinutes( $company->stats_updated_at ) < 15){
    		
			//FRESH -> get from database!
			dd("from db");
			return $company->only( ["position", "points"] )->all();
    	}

		/**
		 * calling ->getExhibitorRankingPosition
		 * Collection of Participants
		 */

    	$exhibitorsWithStats = $source();

		/** Collection of participants */

		$exhibitor = $exhibitorsWithStats->where("company_id", $id)->first();


Log::error("xxx", ["exhibitor" => $exhibitor]);


		if(!$exhibitor){
			 return [];
		}

		$points =  array_get($exhibitor->company->stats, "sessions", 0);
		$position = array_get($exhibitor->company->stats, "position", 0);

		Log::error("yyy", ["stats" => $exhibitor->company->stats]);


		if($points>0){

			$this->update([
				"stats_updated_at" => Carbon::now(),
				"points" => $points,
				"position" => $position
			], $id);
		}

		

		return compact("points", "position");   

    }



}

