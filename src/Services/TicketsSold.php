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
	protected $lowStock = 0.1;

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

	public function all(array $with = []){

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

        if(!empty($with)){
        	$ticketsrepo->with($with);
        }
      
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

		/**
		 * Double check!
		 */

		$ticketpivot = $ticket->ticketpivot->where("sold", 1);

		$ticket->agg = [
			"customers" => $ticketpivot->count(),
			"sold" => $ticketpivot->sum("quantity")
		];

		$ticket->in_dates 	= intval( !$datePassed && !$dateInFuture );
		$ticket->remaining 	= max(0, $ticket->limit - $ticket->agg["sold"]);

		if( $ticket->ticket_group_id > 0) {
			$group = $this->keyedGroups[$ticket->ticket_group_id];
			$remainingInGroup = max(0, $group->limit - $group->agg["sold"]);
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

		//handle INT status
		if($ticket->remaining > 0 && !$datePassed){
			if($dateInFuture){
				$ticket->status = 3;
			}
			if($ticket->in_dates){
				if($ticket->remaining < 3 || $ticket->remaining < $ticket->limit * $this->lowStock){
					$ticket->status = 1;
				}else{
					$ticket->status = 2;
				}
			}
			
		}else{
			$ticket->status = 0;
		}

		$ticket->errors = $errors;
		
		return $ticket;
    }


    /*
    red: (record) => record && !record.bookable,
    orange: (record) => record && record.bookable && record.remaining < record.limit/4,
    blue: (record) => record && record.remaining > 0 && record.errors.includes("future"),
    green: (record) => record && record.bookable,
    */


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