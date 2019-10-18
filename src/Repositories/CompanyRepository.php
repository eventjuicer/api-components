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
    		return [];
    	}

    	if(! is_null($company->stats_updated_at) && 
    		Carbon::now()->diffInMinutes( $company->stats_updated_at ) < 15
    	)
    	{
    		return $company->only( ["position", "points"] );
    	}

    	$stats = $source();

    	$this->update([
    			"stats_updated_at" => Carbon::now(),
    			"points" => array_get($stats, "points", 0),
    			"position" => array_get($stats, "position", 0)
    		], $id);

        //reload!

        return $this->find($id);

    }



}

