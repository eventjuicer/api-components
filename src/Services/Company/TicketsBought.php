<?php

namespace Eventjuicer\Services\Company;


use Carbon\Carbon;
use Eventjuicer\Repositories\EloquentTicketRepository;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\SortByAsc;
use Eventjuicer\Models\Ticket;



class TicketsBought {

    protected $event_id;
    protected $company_id;
    protected $repo;

    function __construct(EloquentTicketRepository $repo){
        $this->repo = $repo;
    }

    function setEventId($event_id){
        $this->event_id = (int) $event_id;
    }

    function setCompanyId($company_id){
        $this->company_id = (int) $company_id;
    }

    public function find($id){
        $repo = clone $this->repo;
        return $repo->find($id);
    }

    public function get(array $rules = ["delayed" => 1]){

        if(empty($this->ticket))

        $repo = clone $this->repo;

        foreach($rules AS $column => $value){
            $repo->pushCriteria(new FlagEquals($column, $value));
        }

        $repo->pushCriteria(new BelongsToEvent(  $this->event_id ));
        $repo->pushCriteria(new SortByAsc( "ticket_group_id" ));

        $repo->with(["ticketpivot.participant", "ticketpivot.purchase", "ticketpivot" => function($q){ $q->where("sold", 1);}]);
        $res = $repo->all();

        $res->map(function($ticket){
           $this->enhance($ticket);
        });

        return $res;

    }

    public function enhance(Ticket $ticket){

        if(empty($this->company_id)){
            //throw new \Exception();
        }

        $soldTickets = $ticket->ticketpivot->where("sold", 1);

        //$participantsWhoBooked = $soldTickets->pluck("participant_id");
        
        $companyPurchases = $soldTickets->groupBy("participant.company_id");

        $currentCompanyPurchases = isset($companyPurchases[ $this->company_id ]) ? $companyPurchases[ $this->company_id  ] : collect([]);

        $companyUnpaidPurchases = $currentCompanyPurchases->where("purchase.paid", 0)->sum("quantity");

        $ticket->date_past     = intval(Carbon::now()->greaterThan( $ticket->end ));

        $ticket->date_future   = intval(Carbon::now()->lessThan( $ticket->start ));

        $ticket->in_dates = intval( !$ticket->date_past && !$ticket->date_future );

        $ticket->remaining = max(0, $ticket->limit - $soldTickets->sum("quantity") );

        //changeable should take into consideration purchases status... if we have STATUS == OK it should not be changeable!
        //company HAS some unpaid purchases related to ticket... change date is valid!

        $ticket->changeable = $companyUnpaidPurchases > 0 && $ticket->change && Carbon::createFromFormat('Y-m-d H:i:s', $ticket->change ) !== false && Carbon::now()->lessThan( $ticket->change ); 

        $ticket->booked =  $currentCompanyPurchases->sum("quantity");

        $ticket->bookable = $ticket->in_dates && $ticket->remaining > 0 ? max(0, min( $ticket->remaining, $ticket->max - $ticket->booked )) : 0;

        $ticket->unpaid = $companyUnpaidPurchases;
        $ticket->transactions = $currentCompanyPurchases;

        return $ticket;

    }

}