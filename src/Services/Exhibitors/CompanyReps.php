<?php

namespace Eventjuicer\Services\Exhibitors;

use Eventjuicer\Models\Company;
use Eventjuicer\Services\Resolver;
use Eventjuicer\Repositories\CompanyRepresentativeRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\ColumnGreaterThanZero;
use Eventjuicer\Repositories\Criteria\SortByDesc;
use Eventjuicer\Services\Personalizer;

class CompanyReps {

	protected $company = null;
	static protected $eventId = 0;

	function __construct(Company $company){
		$this->company = $company;
	}

	static public function setEventId($eventId){
		self::$eventId = $eventId;
	}

	public function get($role = "representative", $enhanced = true){

		if(!$this->company){
			return collect([]);
		}

		if(empty(self::$eventId)){
			$resolver = new Resolver();
			$resolver->fromGroupId($this->company->group_id);
			self::$eventId = $resolver->getEventId();
		}

		$reps = app(CompanyRepresentativeRepository::class);
		$reps->pushCriteria( new BelongsToCompany($this->company->id));
		$reps->pushCriteria( new BelongsToEvent(self::$eventId));
        $reps->pushCriteria( new ColumnGreaterThanZero("parent_id") );
        $reps->pushCriteria( new SortByDesc("id") );
        $reps->with(["fields", "ticketpivot.ticket"]);

        $all = $reps->all();

		$all = $all->filter(function($item) use ($role) {

			$soldTicketsWithRole = $item->ticketpivot->where("sold", 1)->filter(function($ticketpivot) use ($role) {
				return $ticketpivot->ticket->role === $role;
			})->count();

			return $soldTicketsWithRole > 0;

		})->values();

		return $enhanced ? $all->mapInto(Personalizer::class) : $all;

	}
}