<?php


namespace Eventjuicer\Services;


use Illuminate\Database\Eloquent\Model;

//use Illuminate\Contracts\Support\Arrayable;
use Carbon\Carbon;


class TicketEnhancer extends Model {

	protected $ticket;
	protected $companies;

	public $blocked, $buying_companies;

 	function __construct(Model $ticket)
	{
		$this->ticket = $ticket;

		$this->enhance();

		parent::__construct();
		
	}/*eom*/


	public function enhance()
    {

        $this->blocked = $this->ticket->ticketpivot->where("sold", 1);

        //$participantsWhoBooked = $xxxsoxldTickets->pluck("participant_id");
        
        $this->buying_companies = $this->blocked->groupBy("participant.company_id");

        $currentCompanyPurchases = isset($companyPurchases[ $this->user->company()->id ]) ? $companyPurchases[ $this->user->company()->id ] : collect([]);

        $companyUnpaidPurchases = $currentCompanyPurchases->where("purchase.paid", 0)->sum("quantity");

        $datePassed     = Carbon::now()->greaterThan( $ticket->end );

        $dateInFuture   = Carbon::now()->lessThan( $ticket->start );

        $ticket->in_dates = intval( !$datePassed && !$dateInFuture );

        $ticket->remaining = max(0, $ticket->limit - $soldTickets->sum("quantity") );

        //changeable should take into consideration purchases status... if we have STATUS == OK it should not be changeable!
        //company HAS some unpaid purchases related to ticket... change date is valid!

        $ticket->changeable = $companyUnpaidPurchases > 0 && $ticket->change && Carbon::createFromFormat('Y-m-d H:i:s', $ticket->change ) !== false && Carbon::now()->lessThan( $ticket->change ); 

        $ticket->unpaid = $companyUnpaidPurchases;

        $ticket->booked =  $currentCompanyPurchases->sum("quantity");

        $ticket->bookable = $ticket->in_dates && $ticket->remaining > 0 ? max(0, min( $ticket->remaining, $ticket->max - $ticket->booked )) : 0;

        $ticket->transactions = $currentCompanyPurchases;
  
    }


	// function __call();

	// function __get();

	public function assignCompany($companyId)
    {

        $currentCompanyPurchases = isset($this->buying_companies[ $companyId ]) ? $this->buying_companies[ $companyId ] : collect([]);

        $companyUnpaidPurchases = $currentCompanyPurchases->where("purchase.paid", 0)->sum("quantity");

        

        $ticket->changeable = $companyUnpaidPurchases > 0 && $ticket->change && Carbon::createFromFormat('Y-m-d H:i:s', $ticket->change ) !== false && Carbon::now()->lessThan( $ticket->change ); 

        $ticket->unpaid = $companyUnpaidPurchases;

        $ticket->booked =  $currentCompanyPurchases->sum("quantity");

        $ticket->bookable = $ticket->in_dates && $ticket->remaining > 0 ? max(0, min( $ticket->remaining, $ticket->max - $ticket->booked )) : 0;

        $ticket->transactions = $currentCompanyPurchases;

        
    }

}