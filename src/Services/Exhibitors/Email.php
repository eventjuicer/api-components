<?php

namespace Eventjuicer\Services\Exhibitors;

use Eventjuicer\Models\Participant as Model;

class Email {
	
	protected $model;
	protected $admin = null;

	function __construct(Model $model){
		$this->model = $model;
	}

	function getAdmin(){
	
		if($this->model->company->admin_id){
			return $this->model->company->admin->toArray();
		}

		//lookup for default admin

		$defaultUser = $this->model->organizer->users->filter(function($item){

			return $item->pivot->is_default == 1;

		})->first();

		return $defaultUser ? $defaultUser->toArray() : [];


	}



	function getPollUrl(){

		$admin = $this->getAdmin();

		$id = array_get($admin, "id");

		if($id == 16){
			return 'https://forms.gle/DgCoXsnGgnv4nieL6';
			
		}else{
			
			return 'https://forms.gle/Z9GDgyj4Xi49afd26';
		}
	}


	function getSender(){

		$admin = $this->getAdmin();

		return array_get($admin, "fname") . " " . array_get($admin, "lname");
	}

	function getEmail(){

		$admin = $this->getAdmin();

		return array_get($admin, "email");
	}

	function getPhone(){

		$admin = $this->getAdmin();

		return array_get($admin, "phone");
	}

	function getPosition(){

		$admin = $this->getAdmin();

		return array_get($admin, "position");
	}

	function getCalendarUrl(){
		
		$admin = $this->getAdmin();

		return array_get($admin, "calendar");
	}

	function getFooter(){		

		return "

". $this->getSender() . "

" . $this->getPosition() . "

" . $this->getEmail() . " " . $this->getPhone() . "

" . $this->getCalendarUrl() . "

";
	}

}