<?php

namespace Repositories;

use Models\Purchase;
// use Carbon\Carbon;
// use Cache;

//use Services\Repository;
use Bosnadev\Repositories\Eloquent\Repository;

class PurchaseRepository extends Repository
{
    

    public function model()
    {
        return Purchase::class;
    }


    public function createOrUpdate($participant_id = 0, $data)
    {
    	if(! (int) $participant_id)
    	{
    		throw new \Exception("Missing participant info");
    	}

    	//determine event_id from participant

    	$pu = new Purchase;
		$pu->participant_id = $this->participant_id;
		$pu->event_id 		= $active_event_id;
		$pu->group_id 		= 0;
		$pu->organizer_id 	= 0;
		$pu->paid 			= 1;
		$pu->status 		= "OK";
		$pu->createdon 		= Carbon::now()->timestamp;
		$pu->updatedon 		= Carbon::now();

		$this->create();
    }




}