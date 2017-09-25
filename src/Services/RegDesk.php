<?php

namespace Eventjuicer\Services;

use Laravel\Lumen\Routing\ProvidesConvenienceMethods;
use Illuminate\Http\Request;

use Eventjuicer\Repositories\TicketRepository;



class RegDesk {
	
	use ProvidesConvenienceMethods;

	protected $request;
	protected $tickets;

	function __construct(Request $request, TicketRepository $tickets)
	{
		$this->request = $request;
		$this->tickets = $tickets;
	}


	



}