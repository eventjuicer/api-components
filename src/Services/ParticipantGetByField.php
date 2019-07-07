<?php 

namespace Eventjuicer\Services;


use Eventjuicer\Repositories\ParticipantFieldRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\ColumnNotEmpty;


class ParticipantGetByField {
	
	protected $fieldsRepo;

	function __construct(

		ParticipantFieldRepository $fieldsRepo

	)
	{
		$this->fieldsRepo = $fieldsRepo;

	}

    public function get(int $groupId, $withRels = ["participant"], $searchColumn= 250)
    {

        //featured 250
        //logotype 55
        //GET IDS OF TICKETS FROM CURRENT EVENT, FILTERED BY EXHIBITOR ROLE

        $this->fieldsRepo->makeModel();

        $this->fieldsRepo->pushCriteria( new BelongsToGroup( $groupId ));

        $this->fieldsRepo->pushCriteria( new FlagEquals( "field_id", $searchColumn ));
        $this->fieldsRepo->pushCriteria( new FlagEquals( "field_value", 1));

        $this->fieldsRepo->with($withRels);

        $participants = $this->fieldsRepo->all()->pluck("participant");

        return $participants;

       
    }

   

}