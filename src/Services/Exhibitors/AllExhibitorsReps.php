<?php

namespace Eventjuicer\Services\Exhibitors;

use Eventjuicer\Services\Resolver;
use Eventjuicer\Repositories\CompanyRepresentativeRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\ColumnGreaterThanZero;
use Eventjuicer\Repositories\Criteria\SortByDesc;
use Eventjuicer\Services\Personalizer;
use Eventjuicer\Services\Revivers\ParticipantSendable;


class AllExhibitorsReps {

	static protected $eventId = 0;

	protected $sendable;

	protected $dataset = null;

	function __construct(ParticipantSendable $sendable){

		$this->sendable = $sendable;
		$this->sendable->checkUniqueness(true);
        $this->sendable->setMuteTime(20); //minutes!!!!
	}

	static public function setEventId($eventId){
		self::$eventId = $eventId;
	}

	public function getSendable($role = "representatve"){

		if(empty(self::$eventId)){
			throw new \Exception("no EventId set up!");
		}

        $filtered = $this->sendable->filter($this->get($role),  self::$eventId );

        return $filtered;

	}

	public function get($role = "representative", $enhanced = true){


		if(empty(self::$eventId)){
			throw new \Exception("no EventId set up!");
		}

		$reps = app(CompanyRepresentativeRepository::class);
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