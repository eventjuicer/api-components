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
			return $this->model->company->admin->toArray();
		}

		//lookup for default admin

		$defaultUser = $this->model->organizer->users->filter(function($item){

			return $item->pivot->is_default == 1;

		})->first();

		return $defaultUser ? $defaultUser->toArray() : [];


	}

	function getCalendarUrl(){
		
		$admin = $this->getAdmin();

		return array_get($admin, "calendar");
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

	function getFooter(){
		
		$admin = $this->getAdmin();

		return "

" . array_get($admin, "fname") . " " . array_get($admin, "lname") . "

" . array_get($admin, "position") . "

" . array_get($admin, "email") . " " . array_get($admin, "phone") . "

";
	}

}