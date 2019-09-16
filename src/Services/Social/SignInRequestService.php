<?php

namespace Eventjuicer\Services\Social;

use Eventjuicer\Models\SocialSignInRequest;
use Eventjuicer\Models\Group;
use Uuid; //https://github.com/webpatser/laravel-uuid

class SignInRequestService {

	protected $redirect, $service, $project;
	protected $services = ["linkedin"];

	function __construct(){

	}

	public function retrieve($uuid){
		$query = SocialSignInRequest::where("uuid", $uuid)->first();
		return $query;
	}

	function setService($service){
		$this->service =$service;
	}

	function setRedirect($redirect){
		$this->redirect = $redirect;
	}

	function setProject($project){
		$this->project = $project;
	}

	function make(){

		$this->validate();

    	$ssr = new SocialSignInRequest;
    	$ssr->uuid = (string) Uuid::generate(4);
    	$ssr->service = $this->service;
    	$ssr->group_id = $this->project;
    	$ssr->organizer_id = Group::find($this->project)->organizer_id;
    	$ssr->redirect_to = $this->redirect;
    	$ssr->save();

    	return $ssr->uuid;

	}

	protected function validate(){

		$errors = [];

    	if(strpos($this->redirect, "http")===false){
    		$errors[] = "from";
    	}

    	if(!$this->service || !in_array($this->service, $this->services)){
    		$errors[] = "service";
    	}

    	if(!empty($errors)){
    		throw new \Exception(implode(", ", $errors)." parameter(s) missing");
    	}

    	return true;
	}


}