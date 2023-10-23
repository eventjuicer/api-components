<?php

namespace Eventjuicer\Crud\Meetups;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\MeetupRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\SortBy;
use Eventjuicer\Repositories\Criteria\WhereHas;
use Eventjuicer\Repositories\Criteria\WhereIn;

class FetchParticipantMeetups extends Crud  {

    protected $repo;

    function __construct(MeetupRepository $repo){
        $this->repo = $repo;
    }

    public function getForParticipantIds(array $ids){
        $repo = app(MeetupRepository::class);
        $repo->pushCriteria(new WhereIn("participant_id", $ids ));
        $repo->pushCriteria(new FlagEquals("agreed", 1));
        $repo->with([
            "presenter.fieldpivot", 
            "company.data",
            "company.participants.ticketpivot" => function($q){
                $q->where("event_id", $this->getParam("event_id"));
            }
        ]);
        return $repo->all();;
    }

    // public function countVisitorsByEventId($event_id){

    //     $this->repo->pushCriteria(new BelongsToEvent(  $event_id ));
    //     $this->repo->pushCriteria(new WhereHas("purchases.tickets", function($q){
    //         $q->where("role", "visitor");
    //     }));
    //     $res = $this->repo->all(["id"]);
    //    return $res->count();
    // }
 
    // public function getVisitorsByEmail($email = ""){

    //     $email = strtolower( trim($email) );
    //     $event_id = $this->getParam("event_id");

    //     if(!$event_id || !$email){
    //         throw new \Exception("email or event_id missing...");
    //     }

    //     $this->repo->pushCriteria(new BelongsToEvent(  $event_id ));
    //     $this->repo->pushCriteria(new ColumnMatches("email", $email));
    //     $this->repo->pushCriteria(new WhereHas("purchases.tickets", function($q){
    //         $q->where("role", "visitor");
    //     }));
    //     $this->repo->pushCriteria(new SortBy("id", "DESC"));
    //     $this->repo->with(["fields","purchases.tickets"]);
    //     $res = $this->repo->all();
    //    return $res;
    // }


}


