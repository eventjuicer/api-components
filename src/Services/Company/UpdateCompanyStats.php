<?php

namespace Eventjuicer\Services\Company;

use Eventjuicer\Repositories\CompanyRepository;
use Eventjuicer\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UpdateCompanyStats {

	protected $company;
	protected $threshold = 10;

	function __construct(
		Company $company
	){

		$this->company = $company;
	}

	public function statsAreFresh(){

		return 
			(!is_null($this->company->stats_updated_at) && 
			Carbon::now()->diffInMinutes( $this->company->stats_updated_at ) < $this->threshold);
	}

	public function getCachedStats(){
		return [
			"position" => $this->company->position,
			"sessions" => $this->company->points
		];
	}

	public function updateWithRanking(Collection $collection){

		$exhibitor = $collection->where("company_id", $this->company->id)->first();

		if(!$exhibitor){
			return false;
		}

		if(!isset($exhibitor->company->stats)){
			return null;
		}

		app(CompanyRepository::class)->update([
			"stats_updated_at" => Carbon::now(),
			"points" => array_get($exhibitor->company->stats, "sessions"),
			"position" => array_get($exhibitor->company->stats, "position")
		], $this->company->id);

		$this->company->fresh();

	}


}