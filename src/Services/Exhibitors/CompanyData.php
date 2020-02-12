<?php
namespace Eventjuicer\Services\Exhibitors;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\MessageBag;

use Illuminate\Support\Collection;

use Eventjuicer\Models\Participant;
//
use Eventjuicer\ValueObjects\EmailAddress;
use Eventjuicer\ValueObjects\CloudinaryImage;
//
use Eventjuicer\Services\Personalizer;
use Eventjuicer\Services\Resolver;
//
use Eventjuicer\Repositories\CompanyRepresentativeRepository;
use Eventjuicer\Repositories\Criteria\ColumnGreaterThanZero;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\SortByDesc;
use Eventjuicer\Services\Exhibitors\Validator;
use Eventjuicer\Services\Exhibitors\Purchases;
use Eventjuicer\Services\Exhibitors\CompanyReps;

class CompanyData {

	static $eventId;
	protected $model;
	protected $defaultLang = "en";
	protected $messagebag;
	
	protected $prefix = "th4x90iy_";
    protected $campaign = "promoninja";

	function __construct(Model $model){

		$this->model = $model;
		$this->messagebag = new MessageBag;

		if(!isset($this->model->company_id)){
			throw new \Exception("Bad argument provided");
		}
	}

	public function getHostFromGroupId(){

		return (new Resolver())->fromGroupId( $this->getModel()->group_id );
	}

	public function profileUrl(){

		$name = $this->getName() ?? $this->getCompany()->slug;

        return "https://".$this->getHostFromGroupId()."/" .  str_slug(trim($name), "-") .  ",c,". $this->getCompany()->id;

    }

    public function getSafeName(){
    	$name = $this->getName() ?? $this->getCompany()->slug;
    	return str_slug($name, "_");
    }

    public function accountUrl(){
    	return "https://account.".$this->getHostFromGroupId()."/#/login?token=" . $this->getModel()->token;
    }

	public function trackingLink($medium = "banner", $ad = "")
	{

		return sprintf("?utm_source=".$this->prefix."%d&utm_medium=%s&utm_campaign=".$this->campaign."&utm_content=%s", $this->getCompany()->id, $medium, $ad);
	}

	public function trackedProfileUrl($medium = "banner", $ad = ""){

		return $this->profileUrl() . $this->trackingLink($medium, $ad);
	
	}

	//moved from ApiUser
	// public function companyPublicProfile(string $baseHost){

	// 	$name = array_get($this->companyData, "name", $this->company()->slug);

	// 	return rtrim($baseHost, "/") . "/" . str_slug($name, '-') . ",c," . $this->company()->id;

	// }

	public static function setEventId($eventId){
		self::$eventId = $eventId;
	}

	public function setDefaultLang(string $lang){
		$this->defaultLang = $lang;
	}
	
	public function setAccount(string $initials){

	}

	protected function filterFields(Collection $collection, array $limitTo = []){

		return empty($limitTo) ? $collection->all() : $collection->filter(function($value, $name) use ($limitTo){
			return in_array($name, $limitTo);

		})->all();

	}

	public function profileData(array $limitTo = []){

		if(!$this->model->fields){
			return [];
		}

		$arr = $this->model->fields->mapWithKeys(function($item){
                
                return [$item->name => $item->pivot->field_value];
        });

        return $this->filterFields($arr, $limitTo);
	}

	public function getModel(){
		return $this->model;
	}

	public function hasCompany(){
		return !is_null($this->model->company);
	}

	public function getCompany(){
		return $this->model->company;
	}

	public function getCompanyAdminInitials(){

		$company = $this->getCompany();

		if(!$company || !$company->admin){
			return "--undefined--";
		}

		return mb_strtoupper( mb_substr($company->admin->fname, 0, 1) . mb_substr($company->admin->lname, 0, 1) );
	}

	public function companyData(array $limitTo = []){

		if(!$this->getCompany()){
			return [];
		}

		$arr = $this->getCompany()->data->mapWithKeys(function($item){
                	return [$item->name => $item->value];
		});

		return $this->filterFields($arr, $limitTo);
	}

	public function getCompanyDataErrors(){
		
		return (new Validator($this))->status();
	}

	public function getPurchases(){

		if($this->hasCompany()){
			return (new Purchases($this->getCompany()))->fromEvent(self::$eventId);
		}
		
		return collect([]);
	}

	public function logotype(){

        $logotype = new CloudinaryImage($this->getLogotypeCdn());

        if(! $logotype->isValid()){
          $logotype = new CloudinaryImage($this->getLogotype());
        }

        return $logotype;
	}

	/*
	ticketpivot


	["participant_id"]=>
	int(55681)
	["purchase_id"]=>
	int(56814)
	["ticket_id"]=>
	int(1138)
	["event_id"]=>
	int(76)
	["quantity"]=>
	int(1)
	["formdata"]=>
	string(0) ""
	["sold"]=>
	int(0)

      */

	public function getReps($role = "representative", $enhanced = true){

		CompanyReps::setEventId(self::$eventId);

		if($this->hasCompany()){
			return (new CompanyReps($this->getCompany()))->get($role, $enhanced);
		}

		return collect([]);
	}

	public function getMeetups(){

		if($this->hasCompany()){
			return $this->getCompany()->meetups;
		}
		return collect([]);
	}

	public function getLang(){

		$lang = array_get($this->companyData(), "lang", "");
		return strlen($lang)===2 ? $lang : $this->defaultLang;
	}

	public function getEventManager(){
		$event_manager = array_get($this->companyData(), "event_manager", "");
		return ( new EmailAddress($event_manager) )->find();
	}

	public function getFullName(){

		return $this->getFname() . " " . $this->getLname();
	}

	public function hasAccountManager(){
		return ($this->hasCompany() && $this->getCompany()->admin_id > 0);
	}

	function __get($name){
		return $this->model->{$name};
	}

	function __call($fname, $args){

		if( strpos($fname, "get")===0 ){

			$field = Str::snake(str_replace("get", "", $fname));

			//check company data
			$lookup = array_get($this->companyData(), $field, null);

			if( !is_null($lookup) ){
				return $lookup;
			}

			//check profile/registration data
			$lookup = array_get($this->profileData(), $field, null);

			if( !is_null($lookup) ){
				return $lookup;
			}

		}

		// if( strpos($fname, "set")===0 ){

		// 	$field = Str::snake(str_replace("set", "", $fname));

		// 	$lookup = array_get($this->companyData(), $field, null);

		// 	//YET TO BE DONE....
		// }

		if( method_exists($this->model, $fname) ){

			return call_user_func_array(array($this->model, $fname), $args);

		}

		return null;

	}

	// function __callStatic($fname, $args){
	// 	return call_user_func_array(array($this->model, $fname), $args);
	// }

}