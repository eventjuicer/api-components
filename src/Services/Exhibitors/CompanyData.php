<?php
namespace Eventjuicer\Services\Exhibitors;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\MessageBag;
//
use Eventjuicer\ValueObjects\EmailAddress;
use Eventjuicer\Services\Personalizer;
use Eventjuicer\Models\Participant;
//
use Eventjuicer\Repositories\CompanyRepresentativeRepository;
use Eventjuicer\Repositories\Criteria\ColumnGreaterThanZero;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;


class CompanyData extends Model {

	static $eventId;
	protected $model;
	protected $defaultLang = "en";
	protected $messagebag;
	

	function __construct(Model $model){

		$this->model = $model;
		$this->messagebag = new MessageBag;
	}

	public static function setEventId($eventId){
		self::$eventId = $eventId;
	}

	public function setDefaultLang(string $lang){
		$this->defaultLang = $lang;
	}
	
	public function setAccount(string $initials){

	}

	public function profileData(){
		return $this->model->fields ? $this->model->fields->mapWithKeys(function($item){
                
                return [$item->name => $item->pivot->field_value];
        }) : [];
	}

	public function companyData(){
		return $this->model->company ? $this->model->company->data->mapWithKeys(function($item){
                	return [$item->name => $item->value];
			})->all() : [];
	}

	public function getReps(){

		if(!$this->model->company_id || !self::$eventId){
			return collect([]);
		}

		$reps = resolve(CompanyRepresentativeRepository::class);
		$reps->pushCriteria( new BelongsToCompany($this->model->company_id));
		$reps->pushCriteria( new BelongsToEvent(self::$eventId));
        $reps->pushCriteria( new ColumnGreaterThanZero("parent_id") );
        $reps->with(["fields", "purchases"]);
        $reps = $this->reps->all();

		$reps = $reps->filter(function($item){

			return $item->purchases->first()->status !== "cancelled";
		});

		return $reps->mapInto(Personalizer::class);
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