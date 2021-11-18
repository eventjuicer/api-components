<?php 

namespace Eventjuicer\Contracts;

use Illuminate\Http\Request;
use Eventjuicer\Services\TicketsSold;

interface SavesPaidOrder {

	function __construct(Request $request, TicketsSold $ticketdata);

}