<?php 

namespace Eventjuicer\Services;


trait GetByHelperFunctions {
	
    protected $eventId = 0;
    protected $role = "";
    protected $perPage = 25;
    protected $page = 1;
    protected $paginator = null;
    protected $ticketIds = [];
    protected $with = [];
    protected $onlySold = false;

    public function hideCancelled(){
        $this->onlySold = true;
    }

    public function showCancelled(){
        $this->onlySold = false;
    }

    public function setEventId($eventId){
        if($eventId > 0){
            $this->eventId = $eventId;
        }
    }

    public function setRole(string $role){
        if(strlen($role) > 3){
            $this->role = $role;
        }
    }

    public function setTicketId($ticket_id = 0){
        if($ticket_id > 0){
             $this->ticketIds[] = $ticket_id;
        }
    }

    public function setTicketIds(array $ticketIds){
       $this->ticketIds = $ticketIds;
    }

    public function setRelations(array $arr = []){
        $this->with = array_merge($this->with, $arr);
    }

   public function setPage($page){
        if($page > 0){
            $this->page = $page;
        }
   }

   public function setPerPage($perPage){
        if($perPage > 0){
            $this->perPage = $perPage;
        }
   }

   public function total(){
    return $this->paginator ? $this->paginator->total() : 0; 
   }

  




}