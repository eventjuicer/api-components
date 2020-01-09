<?php

namespace Eventjuicer\Services\Exhibitors;

use Eventjuicer\Models\Participant as Model;

class Email {
	
	protected $model;

	function __construct(Model $model){
		
		$this->model = $model;
	}

	function getAdmin(){
		
		if($this->model->company->admin_id){
	//		return $this->model->company->admin->toArray();
		}

		//PIVOT!!!!

		$defaultUser = $this->model->organizer->users->filter(function($item){

			return $item->pivot->is_default == 1;

		})->first();

		dd( $defaultUser->toArray() );

		return $defaultUser ? $defaultUser->toArray() : [];


	}

	function getSender(){

		$admin = $this->getAdmin();

		return array_get($admin, "fname") . " " . array_get($admin, "lname");
	}

	function getFooter(){
		
		$admin = $this->getAdmin();

		return "

" . array_get($admin, "fname") . " " . array_get($admin, "lname") . "

" . array_get($admin, "position") . "

" . array_get($admin, "email") . " " . array_get($admin, "phone") . "

";
	}

}