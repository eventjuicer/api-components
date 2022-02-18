<?php

namespace Eventjuicer\Services;

use Illuminate\Http\Request;
use Eventjuicer\Repositories\ParticipantRepository;

class ApiTokenUser {

    protected $participants;
    protected $token = null;
    protected $user = null;
    protected $company = null;
	
	static $tokenRegExp = "/[a-z0-9]{32,40}/";

	function __construct(ParticipantRepository $participants){
		$this->participants = $participants;
	}
    
    public function setToken($token){
        if($this->validateToken($token)){

            $this->token = $token;

            $this->setUser();

            $this->setCompany();
        }
	}

    protected function setUser(){

		if($this->token){
            // throw new \Exception("No token set...");
            $user = $this->participants->findBy("token", $this->token);
           
            if($user){
                $this->user = $user;
            }
        }
	}

    protected function setCompany(){

        if($this->token && $this->user){
            if($this->user->company_id){
                $this->company = $this->user->company;
            }else if($this->user->parent_id && $this->user->parent && $this->user->parent->company_id){
                $this->company = $this->user->parent->company;
            }
        }
	}
    protected function validateToken($token){

		return preg_match(self::$tokenRegExp, $token);
	}

    public function getToken(){
		return $this->token;
	}

	public function getUser(){
		return $this->user;
	}

	public function getCompany(){
		return $this->company;
	}
	


}