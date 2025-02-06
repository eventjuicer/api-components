<?php

namespace Eventjuicer\Crud\Visitors;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Repositories\VisitorRepository;
use Eventjuicer\Repositories\Criteria\OlderThanDateTime;
use Eventjuicer\Repositories\Criteria\YoungerThanDateTime;
use Eventjuicer\Repositories\Criteria\BelongsToOrganizer;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\SortByDesc;
use Eventjuicer\Re1positories\Criteria\GroupBy;
	
class GetVisitorsForPeriod extends Crud {

    protected $repository;
    protected $organizerId, $groupId, $eventId;
    protected $startDate;
    protected $endDate = "2025-02-18 15:00:00";

    function __construct(VisitorRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get(){
        
        // if(!$this->organizerId && !$this->groupId && !$this->eventId){
        //     throw new \Exception("No scope defined");
        // }

        if($this->startDate){
            $this->repository->pushCriteria(
                new YoungerThanDateTime("created_at", $this->startDate)
            );
        }
        if($this->endDate){
            $this->repository->pushCriteria(
                new OlderThanDateTime("created_at", $this->endDate)
            );
        }
       
        if($this->organizerId){
            $this->repository->pushCriteria(
                new BelongsToOrganizer($this->organizerId)
            );
        }

        if($this->groupId){
            $this->repository->pushCriteria(
                new BelongsToGroup($this->groupId)
            );
        }

        if($this->eventId){
            $this->repository->pushCriteria(
                new BelongsToEvent($this->eventId)
            );
        }

        $this->repository->pushCriteria(
            new GroupBy("company_id", "sessions")
        );
        $this->repository->pushCriteria(
            new SortByDesc("sessions")
        );
        
        return $this->repository->all();


    }

  

    public function setOrganizerId($organizerId){
        if($organizerId > 0){
            $this->organizerId = $organizerId;
        }
    }

    public function setGroupId($groupId){
        if($groupId > 0){
            $this->groupId = $groupId;
        }
    }

    public function setEventId($eventId){
        if($eventId > 0){
            $this->eventId = $eventId;
        }
    }
    
    public function setStartDate($startDate){
        if($this->validateDate($startDate)){
            $this->startDate = $startDate;
        }
      
    }

    public function setEndDate($endDate){
        if($this->validateDate($endDate)){
            $this->endDate = $endDate;
        }
      
    }

    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    

}