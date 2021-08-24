<?php 

namespace Eventjuicer\Services;

use Eventjuicer\Repositories\TicketGroupRepository;
use Eventjuicer\Repositories\EloquentTicketRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\WhereNotIn;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\ColumnMatches;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Eventjuicer\Contracts\CountsSoldTickets;


class TicketsSold implements CountsSoldTickets {

	protected $ticketgroupsrepo;
	protected $ticketsrepo;
	protected $event_id = 0;
	protected $role = "";
	protected $ticket_group_id = 0;
	protected $keyedGroups;

	function __construct(
		TicketGroupRepository $ticketgroupsrepo, 
		EloquentTicketRepository $ticketsrepo
	){
		$this->ticketgroupsrepo = $ticketgroupsrepo;
		$this->ticketsrepo = $ticketsrepo;
	}

	public function setEventId($event_id){
		if($event_id>0){
			$this->event_id = $event_id;
			//it must not be called earlier :)
			$this->keyedGroups = $this->withGroup()->keyBy("id");
		}
	}

	public function setRole($role = ""){
		$this->role = $role;	
	}

	public function setTicketGroupId($ticket_group_id = 0){
		$this->ticket_group_id = $ticket_group_id;
	}

	public function all($with = []){

		if(empty($this->event_id)){
			throw new \Exception("No active event id set!");
		}

		$ticketsrepo = clone $this->ticketsrepo;
        $ticketsrepo->pushCriteria(new BelongsToEvent(  $this->event_id ));

        if($this->role){
        	$ticketsrepo->pushCriteria(new ColumnMatches("role", $this->role));
        }

        if($this->ticket_group_id > 0){
        	$ticketsrepo->pushCriteria(new FlagEquals("ticket_group_id", (int) $this->ticket_group_id));
        }

        //fuck cancelled purchases, we only care about HOLD and OK
        $ticketsrepo->with(array_merge(["ticketpivot" => function($q){ 
        	$q->where("sold", 1);
        }], $with));   
        return $this->enrichCollection( $ticketsrepo->all() );
    }


    public function enrichCollection(Collection $collection){

    	$collection->transform(function($ticket){
    		return $this->enrichTicket($ticket);
    	});

    	return $collection;
    }


    public function enrichTicket($ticket){
    	
		$errors = [];
    	$datePassed = Carbon::now()->greaterThan( $ticket->end );
		$dateInFuture 	= Carbon::now()->lessThan( $ticket->start );

		$ticket->agg = [
			"customers" => $ticket->ticketpivot->count(),
			"sold" => $ticket->ticketpivot->sum("quantity")
		];

		$ticket->in_dates 	= intval( !$datePassed && !$dateInFuture );
		$ticket->remaining 	= $ticket->limit - $ticket["agg"]["sold"];

		if( $ticket->ticket_group_id > 0) {
			$group = $this->keyedGroups[$ticket->ticket_group_id];
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