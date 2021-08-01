<?php 

namespace Eventjuicer\Services;

use Eventjuicer\Repositories\TicketGroupRepository;
use Eventjuicer\Repositories\EloquentTicketRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\WhereNotIn;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TicketsSold {

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

		$ticketsrepo = clone $this->ticketsrepo;
        $ticketsFromTicketGroups = $this->withGroup()->pluck("tickets")->collapse(); 
        //find tickets without ticket group assigned
        $ticketsrepo->pushCriteria(
        	new WhereNotIn( "id", $ticketsFromTicketGroups->pluck("id")->all() )
        );
        $ticketsrepo->pushCriteria(new BelongsToEvent(  $this->event_id ));
        //fuck cancelled purchases, we only care about HOLD and OK
        $ticketsrepo->with(["ticketpivot" => function($q){ 
        	$q->where("sold", 1);
        }]); 
        $merged = $ticketsFromTicketGroups->merge( $ticketsrepo->all() );
        return $this->enrichCollection($merged);
    }

    public function enrichCollection(Collection $collection){

		$keyedGroups = $this->withGroup()->keyBy("id");
    	$collection->transform(function($ticket) use($keyedGroups) {

        	$datePassed 	= Carbon::now()->greaterThan( $ticket->end );
 			$dateInFuture 	= Carbon::now()->lessThan( $ticket->start );
 			$ticket->agg = [
 				"customers" => $ticket->ticketpivot->count(),
 				"sold" => $ticket->ticketpivot->sum("quantity")
 			];
 			$ticket->in_dates 	= intval( !$datePassed && !$dateInFuture );
			if( $ticket->ticket_group_id ) {
	 			$group = $keyedGroups[$ticket->ticket_group_id];
	 			$remainingInGroup = $group->limit - $group->agg["sold"];
	 			$ticket->remaining 	= min($remainingInGroup, ($ticket->limit - $ticket["agg"]["sold"]) );
 			}else{
 				$ticket->remaining 	= $ticket->limit - $ticket->sold;
 			}
 			$ticket->bookable = intval( $ticket->remaining && $ticket->in_dates );
			$errors = [];
			if(! $ticket->in_dates ){
 				if($datePassed){
 					$errors[] = 'overdue';
 				}
 				if(!$datePassed && $dateInFuture){
 					$errors[] = 'future';
 				}
  			}
  			if(! $ticket->remaining > 0 ){
  				$errors[] = 'soldout';
	  			if(isset($remainingInGroup)){
	  				$errors[] = 'soldout_pool';
	  			}
  			}
  			$ticket->errors = $errors;
        	return $ticket;
        });
    	return $collection;
    }


 	public function withGroup(){

 		$ticketgroupsrepo = clone $this->ticketgroupsrepo;
        $ticketgroupsrepo->pushCriteria(new BelongsToEvent( $this->event_id ));
        $ticketgroupsrepo->with(["tickets.ticketpivot" => function($q){ 
        	$q->where("sold", 1);
        }]);
        $all =  $ticketgroupsrepo->all();
        $all->transform(function($group){

        	/**
        	 * ALERT!
        	 * $group->tickets will not be enriched... as we would have infinite loop here
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