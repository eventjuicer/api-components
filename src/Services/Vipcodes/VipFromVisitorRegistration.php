<?php

namespace Eventjuicer\Services\Vipcodes;

use Eventjuicer\Crud\CompanyVipcodes\Fetch;
use Eventjuicer\Crud\CompanyVipcodes\Create;
use Eventjuicer\Services\Vipcodes\ShouldBeExpired;
use Eventjuicer\Models\Participant;

class VipFromVisitorRegistration {

    protected $code;
    protected $participant;
    protected $fetch, $create; 
    protected $vipcode;

    function __construct(Fetch $fetch, Create $create){
        $this->fetch = $fetch;
        $this->create = $create;
    }

    public function setCode(string $code){
        $this->code = $code;
        $this->vipcode = $this->fetch->getByCode($this->code);
    }

    public function setParticipant(Participant $participant){
        $this->participant = $participant;
    }

    public function assign(){

        if(!$this->vipcode){
            throw new \Exception("No vipcode found!");
        }

        $expired = (new ShouldBeExpired($this->vipcode))->check();

        if($expired){
            return false;
        }

        /** OK */

        $this->vipcode->participant_id = $this->participant->id;
        $this->vipcode->save();

        return $this->vipcode->company_id;

    }

    

}

