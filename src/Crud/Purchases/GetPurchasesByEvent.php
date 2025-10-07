<?php

namespace Eventjuicer\Crud\Purchases;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\PurchaseRepository;
// use Eventjuicer\Repositories\ParticipantTicketRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\SortBy;
use Eventjuicer\Repositories\Criteria\Limit;
use Eventjuicer\Repositories\Criteria\FlagNotEquals;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\WhereIn;
use Eventjuicer\Repositories\Criteria\ColumnLessThan;
use Eventjuicer\Repositories\Criteria\ColumnGreaterThan;
// use Eventjuicer\Repositories\Criteria\WhereIn;
use Carbon\Carbon;

class GetPurchasesByEvent extends Crud  {

    protected $repo;

    
    function __construct(PurchaseRepository $repo){
        $this->repo = $repo;
    }

    public function query($event_id){

        $repo = clone $this->repo;

        $this->setData();

        $paidOnly = $this->getParam("free", 0);
        $status = $this->getParam("status", "all");
        $ids = array_filter(explode(",", $this->getParam("ids", "")));
        $statuses = array_filter(explode(",", $this->getParam("statuses", "")));
        $created_at_lt = $this->getParam("created_at_lt", "");
        $preinvoiced = $this->getParam("preinvoiced");
        $invoiced = $this->getParam("invoiced");

        $repo->pushCriteria(new BelongsToEvent($event_id));

        if(!empty($created_at_lt)){
            $repo->pushCriteria(new ColumnLessThan("createdon", 
                Carbon::parse($created_at_lt)->timestamp)
            );
        }

        if($preinvoiced!==null){
            if($preinvoiced){
                $repo->pushCriteria(new ColumnGreaterThan("preinvoice_id", 0));
            }else{
                $repo->pushCriteria(new ColumnLessThan("preinvoice_id", 1));
            }
        }

        if($invoiced!==null){
            if($invoiced){
                $repo->pushCriteria(new ColumnGreaterThan("invoice_id", 0));
            }else{
                $repo->pushCriteria(new ColumnLessThan("invoice_id", 1));
            }
        }

        if(!empty($ids)){
            $repo->pushCriteria(new WhereIn("id", $ids));
        }

        if(!empty($statuses)){
            $repo->pushCriteria(new WhereIn("status", $statuses));
        }
       
        $repo->pushCriteria(
            new SortBy($this->getParam("_sort", "id"), $this->getParam("_order", "DESC")));
       
        if(!$paidOnly){
            $repo->pushCriteria(new FlagNotEquals("amount", 0));
        }
        if($status != "all"){
            $repo->pushCriteria(new FlagEquals("status", $status));
        }

        return $repo;

    }

    public function getPaginated($event_id){

        $take = $this->getParam("_end", 25) - $this->getParam("_start", 0);

        $repo  = $this->query($event_id);
        
        $repo->with(["tickets", "participant"]);
        $repo->pushCriteria(
            new Limit($take, $this->getParam("_start", 0))
        );

        return $repo->all();
    }


    public function getAll($event_id){
        $repo  =$this->query($event_id);
        $repo->with(["tickets"]);
        return $repo->all();
    }

    public function getCount($event_id){
        $repo  = $this->query($event_id); 
        return $repo->all()->count();
    }

    

}

