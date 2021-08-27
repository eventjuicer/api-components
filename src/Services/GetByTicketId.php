<?php 

namespace Eventjuicer\Services;


use Eventjuicer\Repositories\ParticipantTicketRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\SortByDesc;
use Eventjuicer\Repositories\Criteria\WhereIn;
use Eventjuicer\Repositories\Criteria\ColumnMatches;

class GetByTicketId   {
	
    use GetByHelperFunctions;

	protected $repo;

	function __construct(ParticipantTicketRepository $repo){
		$this->repo = $repo;
	}

   public function get(){

    $this->setRelations([
        "participant.ticketpivot", 
        "participant.purchases", 
        "participant.fields"
    ]);
  
    $this->repo->pushCriteria(new WhereIn("ticket_id", $this->ticketIds));
    $this->repo->pushCriteria(new SortByDesc("participant_id"));
    $this->paginator = $this->repo->paginate($this->perPage, ["*"], $this->page);
    $this->repo->with($this->with);
    $res = $this->repo->all();
    return $res->pluck("participant");
   }

   

}