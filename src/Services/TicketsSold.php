<?php 

namespace Eventjuicer\Services;

use Eventjuicer\Repositories\TicketGroupRepository;
use Eventjuicer\Repositories\EloquentTicketRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\WhereNotIn;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Eventjuicer\Contracts\CountsSoldTickets;


class TicketsSold implements CountsSoldTickets {

	protected $ticketgroupsrepo;
	protected $ticketsrepo;
	protected $event_id = 0;

	function __construct(
		TicketGroupRepository $ticketgroupsrepo, 
		EloquentTicketRepository $ticketsrepo
	){
		$this->ticketgroupsrepo = $ticketgroupsrepo;
		$this->ticketsrepo = $ticketsrepo;
	}

	public function setEventId($event_id){
		$this->event_id = $event_id;
	}


	public function all(){

		if(empty($this->event_id)){
			throw new \Exception("No active event id set!");
		}

		$ticketsrepo = clone $this->ticketsrepo;
        $ticketsrepo->pushCriteria(new BelongsToEvent(  $this->event_id ));
        //fuck cancelled purchases, we only care about HOLD and OK
        $ticketsrepo->with(["ticketpivot" => function($q){ 
        	$q->where("sold", 1);
        }]);   
        return $this->enrichCollection( $ticketsrepo->all() );
    }


    public function enrichCollection(Collection $collection){

		$keyedGroups = $this->withGroup()->keyBy("id");

    	$collection->transform(function($ticket) use($keyedGroups) {
    		$errors = [];
        	$datePassed 	= Carbon::now()->greaterThan( $ticket->end );
 			$dateInFuture 	= Carbon::now()->lessThan( $ticket->start );

 			$ticket->agg = [
 				"customers" => $ticket->ticketpivot->count(),
 				"sold" => $ticket->ticketpivot->sum("quantity")
 			];

 			$ticket->in_dates 	= intval( !$datePassed && !$dateInFuture );
 			$ticket->remaining 	= $ticket->limit - $ticket["agg"]["sold"];

			if( $ticket->ticket_group_id > 0) {
	 			$group = $keyedGroups[$ticket->ticket_group_id];
	 			$remainingInGroup = $group->limit - $group->agg["sold"];
	 			if($remainingInGroup < $ticket->remaining){
	 				$ticket->remaining = $remainingInGroup;
	 			}
	 			if(! ($remainingInGroup > 0) ){
	 				$errors[] = 'soldout_pool';
	 			}
 			}

 			$ticket->bookable = intval( $ticket->remaining>0 && $ticket->in_dates );

			if(! $ticket->in_dates ){
 				if($datePassed){
 					$errors[] = 'overdue';
 				}
 				if(!$datePassed && $dateInFuture){
 					$errors[] = 'future';
 				}
  			}
  			if(! ($ticket->remaining > 0) ){
  				$errors[] = 'soldout';
  			}
  			$ticket->errors = $errors;
        	return $ticket;
        });
    	return $collection;
    }


 	public function withGroup(){

 		if(empty($this->event_id)){
			throw new \Exception("No active event id set!");
		}

 		$ticketgroupsrepo = clone $this->ticketgroupsrepo;
        $ticketgroupsrepo->pushCriteria(new BelongsToEvent( $this->event_id ));
        $ticketgroupsrepo->with(["tickets.ticketpivot" => function($q){ 
        	$q->where("sold", 1);
        }]);
        $all =  $ticketgroupsrepo->all();
        $all->transform(function($group){

        	/**
        	 * ALERT!
        	 * $group->tickets will NOT be enriched... as we would have infinite loop here
        	 * $groups->transform(function($ticketgroup){
        	 	* $ticketgroup->tickets = $this->ticketsold->enrichCollection($ticketgroup->tickets);
        	 	* return $ticketgroup;
        	 	* });
        	 **/

            $purchases = $group->tickets->pluck("ticketpivot")->collapse();
            $group->agg  = [
                  "offered"     => $group->tickets->sum("limit"),
                  "sold"        => $purchases->sum("quantity"),
                  "customers"   => $purchases->count()
            ];
            return $group;
        });

        return $all;
    }


}