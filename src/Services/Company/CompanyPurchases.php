<?php

namespace Eventjuicer\Services\Company;

use Eventjuicer\Models\Company;
use Eventjuicer\Repositories\ParticipantRepository;
// use Eventjuicer\Repositories\Criteria\CompanyRepository;
use Eventjuicer\Repositories\Criteria\RelTableHas;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;

class CompanyPurchases {

    protected $company;
    protected $event_id;

    function __construct(Company $company){
        $this->company = $company;
    }

    function setEventId($event_id){
        $this->event_id = (int) $event_id;
    }

    function get(){

        $repo = app(ParticipantRepository::class);
        $repo->pushCriteria(new BelongsToCompany($this->company->id));
        $repo->pushCriteria(new BelongsToEvent($this->event_id));

        $repo->with(["ticketpivot" => function($q){
           $q->where("sold", 1);
        }, "ticketpivot.ticket"]);
        $res = $repo->all()->pluck("ticketpivot")->collapse()->pluck("ticket");
        
       return $res;
    }

    
}



// $ticket_id = intval( $request->input("ticket_id") );

// if($ticket_id){

//     $pivot->pushCriteria(new FlagEquals("ticket_id", $ticket_id));

// }else{

//      // $pivot->pushCriteria(new FlagEquals("event_id",  $this->user->activeEventId() ));
// }

// $pivot->with(["purchase.ticketpivot.ticket", "purchase.participant", "purchase.event"]);
// $pivot->pushCriteria(new WhereIn("participant_id", $this->allCompanyUsers));
// $pivot->pushCriteria(new SortByDesc( "purchase_id" ));
// $res = $pivot->all()->pluck("purchase");

// //filter representatives...!

// $res = $res->filter(function($item){
    
//     $role = $item->ticketpivot->pluck("ticket.role");
    
//     if( $role->contains("representative") ){
//         return false;
//     }
//     if( $role->contains("party") ){
//         return false;
//     }
//     return true;

// })->values();

