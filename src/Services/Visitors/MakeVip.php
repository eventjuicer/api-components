<?php 

namespace Eventjuicer\Services\Visitors;

use Eventjuicer\Services\SaveOrder;
use Eventjuicer\Models\Participant;

class MakeVip {

	protected $saveorder; 
	function __construct(SaveOrder $saveorder){
		$this->saveorder = $saveorder;


	}

	function make(Participant $participant){

		$this->saveorder->make();
	
	}


}