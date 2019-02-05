<?php 

namespace Eventjuicer\Services;


use Eventjuicer\Repositories\EloquentTicketRepository;
use Eventjuicer\Repositories\ParticipantTicketRepository;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\WhereIn;

class GetByRole {
	
	protected $ticketsRepo, $participantTicketRepo, $participantsRepo;

	function __construct(

		EloquentTicketRepository $ticketsRepo, 
    	ParticipantTicketRepository $participantTicketRepo,
    	ParticipantRepository $participantsRepo

	)
	{
		$this->ticketsRepo = $ticketsRepo;
		$this->participantTicketRepo = $participantTicketRepo;
		$this->participantsRepo = $participantsRepo;
	}

	public function get(int $eventId, string $role, $withRels = [], $ticketColumn="role")
	{
			//GET IDS OF TICKETS FROM CURRENT EVENT, FILTERED BY EXHIBITOR ROLE
    	$this->ticketsRepo->pushCriteria( new BelongsToEvent( $eventId ));
    	$this->ticketsRepo->pushCriteria( new FlagEquals( $ticketColumn, $role ));
    	$ticketIds = $this->ticketsRepo->all()->pluck("id")->all();

    	if(empty($ticketIds))
    	{
    		return collect([]);
    	}

    	//GET PARTICIPANTS FILTERED BY ABOVE TICKETS AND PAID STATUS
    	$this->participantTicketRepo->pushCriteria( new WhereIn("ticket_id", $ticketIds ));
    	$this->participantTicketRepo->pushCriteria( new FlagEquals("sold", 1 ));
    	$participantIds = $this->participantTicketRepo->all()->pluck("participant_id")->all();

    	if(empty($participantIds))
    	{
    		return collect([]);
    	}

        return $this->getParticipantsByIds($participantIds, $withRels);
	}

    public function getParticipantsByIds(array $participantIds, $withRels = []){

        //GET PARTICIPANTS 
        $this->participantsRepo->pushCriteria( new WhereIn("id", $participantIds));
        if(!empty($withRels) && is_array($withRels))
        {
            $this->participantsRepo->with($withRels);
        }
        return $this->participantsRepo->all();

    }

}