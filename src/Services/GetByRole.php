<?php 

namespace Eventjuicer\Services;


use Eventjuicer\Repositories\EloquentTicketRepository;
use Eventjuicer\Repositories\ParticipantTicketRepository;
use Eventjuicer\Repositories\ParticipantFieldRepository;
use Eventjuicer\Repositories\ParticipantRepository;

use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\WhereIn;
use Eventjuicer\Repositories\Criteria\ColumnMatches;



class GetByRole {
	
	protected $ticketsRepo, $participantTicketRepo, $participantsRepo, $fieldsRepo;

	function __construct(

		EloquentTicketRepository $ticketsRepo, 
    	ParticipantTicketRepository $participantTicketRepo,
    	ParticipantRepository $participantsRepo,
        ParticipantFieldRepository $fieldsRepo

	)
	{
		$this->ticketsRepo = $ticketsRepo;
		$this->participantTicketRepo = $participantTicketRepo;
		$this->participantsRepo = $participantsRepo;
        $this->fieldsRepo = $fieldsRepo;
	}


    public function getParticipantIdsByGroup(int $groupId, string $role, $ticketColumn= "role")
    {
            //GET IDS OF TICKETS FROM CURRENT EVENT, FILTERED BY EXHIBITOR ROLE

        $this->ticketsRepo->makeModel();

        $this->ticketsRepo->pushCriteria( new BelongsToGroup( $groupId ));
        $this->ticketsRepo->pushCriteria( new FlagEquals( $ticketColumn, $role ));
        $ticketIds = $this->ticketsRepo->all()->pluck("id")->all();

        if(empty($ticketIds))
        {
            return [];
        }

        $participantIds = $this->getPurchasesFromTickets($ticketIds);

        return empty($participantIds) ? [] : $participantIds;

    }


	public function get(int $eventId, string $role, $withRels = [], $ticketColumn="role")
	{

         $this->ticketsRepo->makeModel();
         
			//GET IDS OF TICKETS FROM CURRENT EVENT, FILTERED BY EXHIBITOR ROLE
    	$this->ticketsRepo->pushCriteria( new BelongsToEvent( $eventId ));
    	$this->ticketsRepo->pushCriteria( new FlagEquals( $ticketColumn, $role ));
    	$ticketIds = $this->ticketsRepo->all()->pluck("id")->all();

    	if(empty($ticketIds))
    	{
    		return collect([]);
    	}

    	$participantIds = $this->getPurchasesFromTickets($ticketIds);

    	if(empty($participantIds))
    	{
    		return collect([]);
    	}

        return $this->getParticipantsByIds($participantIds, $withRels);
	}

    public function getParticipantIdsByField(array $participantIds, array $rules = []){

         //GET PARTICIPANTS FILTERED BY ABOVE TICKETS AND PAID STATUS
        $this->fieldsRepo->pushCriteria( new WhereIn("participant_id", $participantIds ));

        foreach($rules as $columnName => $columnValue){
             $this->fieldsRepo->pushCriteria( new FlagEquals($columnName, $columnValue));
        }
     
        return $this->fieldsRepo->all()->pluck("participant_id")->all();
    }

    public function getPurchasesFromTickets(array $ticketIds){

        if(empty($ticketIds)){
            return [];
        }

         //GET PARTICIPANTS FILTERED BY ABOVE TICKETS AND PAID STATUS
        $this->participantTicketRepo->pushCriteria( new WhereIn("ticket_id", $ticketIds ));
        //get cancelled as well
        // $this->participantTicketRepo->pushCriteria( new FlagEquals("sold", 1 ));
        return $this->participantTicketRepo->all()->pluck("participant_id")->all();

    }

    public function getParticipantsByIds(array $participantIds, array $withRels = []){

        //GET PARTICIPANTS 
        $this->participantsRepo->pushCriteria( new WhereIn("id", $participantIds));
        if(!empty($withRels))
        {
            $this->participantsRepo->with($withRels);
        }
        return $this->participantsRepo->all();

    }

}