<?php

namespace Eventjuicer\Services;

use Illuminate\Http\Request;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\CompanyRepository;
use Eventjuicer\Models\Group;
use Eventjuicer\Models\Event;

use Eventjuicer\Contracts\Email\Templated;

use Eventjuicer\Repositories\Criteria\ColumnMatches;
use Eventjuicer\Repositories\Criteria\ColumnGreaterThanZero;
use Eventjuicer\Repositories\Criteria\RelHasNonZeroValue;

use Eventjuicer\ValueObjects\EmailAddress;
use Eventjuicer\Services\Personalizer;

class ApiUser {
	
	protected $request, $json, $participants, $token, $user, $company, $representative;
	
	static $tokenRegExp = "/[a-z0-9]{32,40}/";

	function __construct(

		Request $request, 
		ParticipantRepository $participants
		)
	{
		$this->request = $request;
		$this->participants = $participants;
	

		$token = $request->header("x-token", 
			$request->input("x-token", null)
		);

		$this->setToken($token);

	}

	public function getToken()
	{
		return $this->token;
	}

	public function switchToParent()
	{
		//is it sub account?
		if($this->parent_id)
		{
			$this->representative = clone $this;

			$this->setToken($this->parent->token);

			return true;
		}

		return false;
		
	}


	public function personalize($str)
	{
		$this->switchToParent();

		return (string) new Personalizer($this->user(), $str);
	}

	public function logotype()
	{
		$this->switchToParent();

		return (string) new Personalizer($this->user(), "[[logotype]]");
	}

	public function trackingLink($medium = "banner", $ad = "")
	{
         return sprintf("?utm_source=company_%d&utm_medium=%s&utm_campaign=teh15c&utm_content=%s", $this->company()->id, $medium, $ad);
	}


	public function realUser()
	{

		return is_object($this->representative) ? $this->representative->user()->fresh() : $this->user();
	}

	public function cacheKey($part = "")
	{
		return md5($this->company()->id . "_". $part);
	}

	public function setToken($token)
	{

		$this->token = $token;

		if($this->tokenIsValid())
		{
			$this->setUser();
		}
	}

	public function user()
	{
		return $this->user;
	}


	public function company()
	{
		return $this->company;
	}

	public function companyData($access = "company")
	{

		if( empty( $this->company() ) || empty( $this->company()->id ) )
		{
			return [];
		}

		return $this->company()->data->where("access", $access)->mapWithKeys(function($_item){
                
                return [$_item->name => $_item->value];

        })->all();
	}

	public function setting($name = "lang"){

		$filtered = $this->company()->data->where("name", $name);

		if($filtered->count()){
			return $filtered->first()->value;
		}
		
		return "";
	}

	public function companyFormdata($eventId = 0)
	{

		$this->company()->load("participants.ticketpivot");
	
		return $this->company()->participants->pluck("ticketpivot")->collapse()->where("sold", 1)->when($eventId > 0, function($collection) use ($eventId){
				return $collection->where("event_id", $eventId);
			})->pluck("formdata")->all();
	}

	public function companyPublicProfile(){

		$name = array_get($this->companyData, "name", $this->company()->slug);

		return 'https://targiehandlu.pl/' . str_slug($name, '-') . ",c," . $this->company()->id;

	}


	public function __get($attr)
	{



		return $this->user ? $this->user->{$attr} : 0; 
	}

	
	public function canAccess($obj)
	{

		if(isset($obj->company_id) && $obj->company_id > 0)
		{
			return $this->company()->id == $obj->company_id;
		}

		if($obj->group_id > 0)
		{
			return $this->group_id == $obj->group_id;
		}

		if(isset($obj->event_id) && $obj->event_id > 0)
		{

			return $this->group_id == Event::find($obj->event_id)->group_id;
		}


		return false;
		
	}


	public function assignToCompany($company_id)
	{

		if(!$company_id || $this->parent_id)
		{
			throw new \Exception("cannot assign a company...");
		}


		$this->participants->update(compact("company_id"), $this->user->id);

		//we have to refresh our data, right????
		$this->setUser();

	}

	//scan or participants... check unique event_ids...!

	public function accessibleEvents()
	{

	}

	public function slug()
	{
		return str_slug((new EmailAddress($this->user->email))->domain());

	}

	public function check()
	{
		if(!$this->isValid())
		{
			abort(404);
		}
	}


	public function isValid()
	{
		return ( $this->user && $this->id && $this->group_id);
	}

	public function log()
	{

	}

	public function activeEventId($strict = false)
	{	

		if($this->company)
		{
			return (int) Group::findOrFail( $this->company->group_id )->active_event_id;
		}

		if($strict)
		{
			return false;
		}

		return (int) Group::findOrFail( $this->group_id )->active_event_id;
	}

	protected function setUser()
	{

		if($this->tokenIsValid())
		{
			$this->user = $this->participants->findBy("token", $this->token);

			$this->setCompany();
		}

	}


	protected function setCompany()
	{
		$this->company = null;

		if($this->user)
		{
			if($this->user->parent_id && $this->user->parent->company_id)
			{
				$this->company = $this->user->parent->company;

				return true;
			} 

			if($this->user->company_id)
			{
				$this->company = $this->user->company;

				return true;
			}
		}
		
	}
	
	public function tokenIsValid()
	{

		return $this->token && preg_match(self::$tokenRegExp, $this->token);
      
	}


}