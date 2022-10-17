<?php

namespace Eventjuicer\Crud\CompanyMeetups;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\Participant;
use Eventjuicer\Repositories\MeetupRepository;


class CreateByParticipant extends Crud  {

    protected $repo;
    
    function __construct(MeetupRepository $repo){
        $this->repo = $repo;

        
    }

    function validates(){
        
        return $this->isValid([
            'company_id' => 'required|numeric|digits_between:1,20'
        ]);

    }

    public function create(Participant $participant){

        if(!$this->validates()){
            return null;
        }

       $data = array();

        $data["direction"] = "P2C";
        $data["organizer_id"] = $participant->organizer_id;
        $data["group_id"] = $participant->group_id;
        $data["event_id"] = $participant->event_id;
        $data["company_id"] =  (int) $this->getParam("company_id");;
        $data["participant_id"] = $participant->id;
        $data["user_id"] = 0; 
        $data["creative_id"] = 0;
        $data["agreed"] = 0;
        $data["retries"] = 0;
        $data["message"] = "";
        $data["data"] = array();
        $data["comment"] = "";
        
        $this->repo->saveModel($data);

        return $this->find( $this->repo->getId() );
    }




}


