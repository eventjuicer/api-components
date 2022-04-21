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

    if($this->onlySold){
        $this->repo->pushCriteria(new FlagEquals("sold", 1));
    }

    $this->paginator = $this->repo->paginate($this->perPage);
    $this->repo->with($this->with);
    $res = $this->repo->all();
    return $res->pluck("participant");
   }

   

}


/**
 * 
 * 1	participant_id	int unsigned	NULL	NULL	NO	0			
 * 2	purchase_id	int unsigned	NULL	NULL	NO	0			
 * 3	ticket_id	int unsigned	NULL	NULL	NO	0			
 * 4	event_id	int unsigned	NULL	NULL	NO	0			
 * 5	quantity	smallint	NULL	NULL	NO	0			
 * 6	formdata	text	utf8	utf8_polish_ci	NO	NULL			
 * 7	sold	tinyint unsigned	NULL	NULL	NO	1			
 * 
 */