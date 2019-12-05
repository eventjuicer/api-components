<?php

namespace Eventjuicer\Services\Social;

use Eventjuicer\Models\SocialSignInRequest;
use Eventjuicer\Models\Group;
use Eventjuicer\Models\Organizer;
//use Eventjuicer\Services\Cascaded\Setting;

use Uuid; //https://github.com/webpatser/laravel-uuid

class SignInRequestService {

	protected $state;
	protected $redirect, $service, $project;
	protected $services = ["linkedin"];
	//protected $settings;
	protected $session = "";

	function __construct(){
		//$this->settings = $settings;
	}

	public function retrieve(){

		if(empty($this->state)){
			return null;
		}
		
		$query = SocialSignInRequest::where("uuid", $this->state)->first();
		return $query;
	}

	public function release(){
		$row = $this->retrieve();
		return $row ? $row->delete() : null;
	}

	function setState($state){
		$this->state = $state;
	}

	function setSession($session){
		$this->session = $session;
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

	function getOrganizer(){

		$row = $this->retrieve();
		return $row ? $row->organizer_id : 0;
	}

	function getGroup(){
		$row = $this->retrieve();
		return $row ? $row->group_id : 0;
	}

	function make(){

		$this->validate();

    	$ssr = new SocialSignInRequest;
    	$ssr->uuid = (string) Uuid::generate(4);
    	$ssr->service = $this->service;
    	$ssr->group_id = $this->project;
    	$ssr->session = $this->session;
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
    		throw new \Exception(implode(", ", $errors)." parameter(s) error");
    	}

    	return true;
	}

	public function getSetting(string $name){


		//$settings = $this->settings->cascaded($eventId, "site");

		//settings should be cascaded!... 

		if(empty($this->state)){
			return null;
		}

        $organizer = Organizer::findOrFail( $this->getOrganizer() );

        if($organizer && $organizer->settings){
          $setting = $organizer->settings->where("name", $name)->first();
          return $setting ? json_decode($setting->data, true) : null;
        }
        return null;
    }



}