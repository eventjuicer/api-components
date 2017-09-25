<?php

namespace Eventjuicer\Services\UserInvitator;

//Facades
use Request;
use Input;
use Validator;

//model
use Eventjuicer\UserInvitation;


//Errors-related 
use Illuminate\Support\MessageBag;
//use Illuminate\Foundation\Validation\ValidatesRequests;




use Illuminate\Foundation\Bus\DispatchesJobs;

//JOB
use Eventjuicer\Services\UserInvitator\Jobs\ResendUserInvite;




//JOB-related 

use Illuminate\Support\Collection;

//use Illuminate\Contracts\Mail\Mailer;


use Eventjuicer\Services\UserInvitator\UserInvitationResender AS Resender;



class UserInvitator
{

	
	private $token, $email;

	static $email_field 		= "email";
	static $invitation_param 	= "invitation";

	private $errors;

 	use DispatchesJobs;

	function __construct(MessageBag $errors)
	{
		
		$this->errors = $errors;

		$this->token = Input::get(self::$invitation_param);
		$this->email = Request::input(self::$email_field);
	}

	public function tokenExists()
	{
		if(empty($this->token) || strlen($this->token) < 5)
		{
			$this->errors->add("code", "Invitation link broken");
			return false;
		}
		
		return true;
	}

	public function tokenIsValid()
	{
		//check in the database

		$invitation = UserInvitation::find($this->token);

		if(empty($invitation))
		{
			$this->errors->add("code", "We couldn't find your invitation");
			return null;
		}

		if(! $invitation->codeIsActive() )
		{
			$this->errors->add("code", "This code is inactive!");
			return false;
		}

		if(! $invitation->codeIsActive() )
		{
			$this->errors->add("code", "This code is inactive!");
			return false;
		}

	
		return $invitation;
 
	}


	public function checkAddress()
	{
		
		$validator = Validator::make(array("email"=>$this->email), [
	  		'email' => 'required|email'
		]);

		if( $validator->fails() ) 
		{
			$this->errors->add("email", "Are you sure it's a valid email? :)");
            return false;
        }

		return true;		
	}
	

	public function findInvitationForEmail()
	{

		$invitations = UserInvitation::where(array("email" => $this->email))->get();

		if($invitations->isEmpty())
		{
			$this->errors->add("code", "We couldn't find any invitations associated with this email");
			return null;
		}

		$this->restoreInvitations( $invitations );

		return true;

	}


	



	public function errors()
	{	

		/*
		
		$validator = Validator::make(...);

		$validator->after(function($validator) {
		if ($this->somethingElseIsInvalid()) {
		$validator->errors()->add('field', 'Something is wrong with this field!');
		}
		});

		if ($validator->fails()) {
		//
		}

		*/
		
		// $this->validator->messages()

		return $this->errors;
	}

	private function restoreInvitations(Collection $invitations )
	{

		//we iterate over each invitation and resend emails!
		foreach($invitations AS $invitation)
		{
			

			$this->dispatch( new ResendUserInvite( $invitation )  );
		}
	}


}