<?php 

namespace Eventjuicer\Services;


use Eventjuicer\Repositories\ParticipantTicketRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\SortByDesc;
use Eventjuicer\Repositories\Criteria\WhereIn;
use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\WhereHas;

class GetByRoleNew   {
	
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

        $this->repo->pushCriteria( new SortByDesc( "participant_id" ));
        $this->repo->pushCriteria( new WhereHas("ticket", function($q){
            $q->where("role", $this->role);
            $q->where("event_id", $this->eventId);
        }));
        $this->repo->with($this->with);
        $this->paginator = $this->repo->paginate(1);

        $res = $this->repo->all();

        return $res->pluck("participant");

   }

   

}