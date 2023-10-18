<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Company;
use Eventjuicer\Repositories\Repository;

use Carbon\Carbon;
use Closure;

class CompanyRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return Company::class;
    }


    public function updateStatsIfNeeded($id, Closure $source)
    {

    	$company = $this->find($id);

    	if(!$company) 
    	{
    		throw new \Exception("Company missing...");
    	}

    	if(! is_null($company->stats_updated_at) && Carbon::now()->diffInMinutes( $company->stats_updated_at ) < 15){
    		
			//FRESH -> get from database!
			return $company->only( ["position", "points"] );
    	}

    	$exhibitorsWithStats = $source();

		/** Collection of participants */

		$exhibitor = $exhibitorsWithStats->where("company_id", $id)->first();

		if(!$exhibitor){
			throw new \Exception("Company $id missing...");
		}


		$points =  array_get($exhibitor->company->stats, "sessions", 0);
		$position = array_get($exhibitor->company->stats, "position", 0);

		$this->update([
			"stats_updated_at" => Carbon::now(),
			"points" => $points,
			"position" => $position
		], $id);

		return $this->find($id);   

    }



}

