<?php
namespace Eventjuicer\Services\Exhibitors;

use Eventjuicer\Services\Resolver;
use Eventjuicer\Services\GetByRole;
use Eventjuicer\Services\Exhibitors\CompanyData;
use Eventjuicer\Services\Revivers\ParticipantSendable;
use Illuminate\Support\MessageBag;
use Eventjuicer\Services\PartnerPerformanceLocal;
use Validator;

class Console {

	protected $eventId = 0;
	protected $groupId = 0;
	protected $organizerId = 0;
	protected $repo;
	protected $dataset = [];
	protected $sendable;
	protected $params, $requirements = [];
	protected $messagebag;
	protected $performance;
	protected $additionalRels = ["fields", "company.data", "company.participants"];
	protected $additionalRepRels = ["fields", "company.data"];
	protected $errors = [];

	function __construct(
		GetByRole $repo, 
		ParticipantSendable $sendable,
		MessageBag $messagebag,
		PartnerPerformanceLocal $performance
	){

		$this->repo = $repo;
		$this->sendable = $sendable;
	 	$this->messagebag = $messagebag;
	 	$this->performance = $performance;

		$this->sendable->checkUniqueness(false);

        $this->sendable->setMuteTime(20); //minutes!!!!
	
	}

	public function setParams(array $params = []){
		$this->params = $params;
	}

	public function getParam(string $key){

		if(!isset($this->params[$key])){
			throw new \Exception("No $key param.");
		}

		return $this->params[$key];
	}

	public function isValid(array $rules = []){
        $validator = Validator::make($this->params, $rules);
		if($validator->passes()){
			return true;
		}
		throw new \Exception($validator->errors());
    }



	public function validateParams(){


		// if(empty($viewlang)) {
        //     $this->messagebag->add("lang", "--lang= must be set!");
        // }


        // if(empty($domain)) {
        //     $errors[] = "--domain= must be set!";
        // }

        // if(empty($subject)) {
        //     $errors[] = "--subject= must be set!";
        // }
    
        
        // if(empty($email)) {
        //     $errors[] = "--email= must be set!";
        // }

        // if(empty($defaultlang)) {
        //     $errors[] = "--defaultlang= must be set!";
        // }

        // $email = $email . "-" . $viewlang;

        // if(! view()->exists("emails.company." . $email)) {
        //     $errors[] = "--email= error. View cannot be found!";
        // }



		// if(isset($this->params["email"])){
		// 	dd("email");
		// }

		// if(isset($this->params["domain"])){
		// 	dd("email");
		// }

	}

	
	public function getApi($resource = "", string $host){

        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );  

        //TODO - map group_id to host!

    	$json = json_decode(file_get_contents("https://api.eventjuicer.com/v1/public/hosts/".$host."/".trim($resource, " /"), false, stream_context_create($arrContextOptions)), true);

    	if(empty($json) || empty($json["data"])) {
        	return ["data" => []];
    	}

    	return $json;

     
	}

	public function getTranslations(){

		if($this->getGroupId()>1){
			//expojuicer
			return json_decode(file_get_contents("https://localise.biz/api/export/all.json?format=multi&key=Hv2J57NdQBgu3UYbz7DuXmAlU2KZzGYz"), true);
		}else{
			return json_decode(file_get_contents("https://localise.biz/api/export/all.json?format=multi&key=LKwL-Ej08phbpT-bbw8_Furw0eUQqeAs"), true);
		}
	}

	public function run(string $domain = "", $previous = false){

		if(empty($domain) && !empty($this->params["domain"])){
			$domain = $this->params["domain"];
		}

        $route = new Resolver( $domain );

        $this->eventId = $previous ? $route->previousEvent() : $route->getEventId();
        $this->groupId = $route->getGroupId();
		$this->organizerId = $route->getOrganizerId();
	}

	public function setEventId($eventId){

		$this->eventId = $eventId;

		$route = new Resolver();
		
		$route->fromEventId($eventId);

		$this->groupId = $route->getGroupId();
	
	}

	public function getEventId(){

		return $this->eventId;
	}

	public function getGroupId(){

		return $this->groupId;
	}

	public function getOrganizerId(){

		return $this->organizerId;
	}

	public function getSendable($uniqueCompanies=true){

        $filtered = $this->sendable->filter($this->getDataset($uniqueCompanies), $this->getEventId());

        return $filtered;

	}

	public function setDatasetRels(array $additionalRels){
		foreach($additionalRels as $rel){
			$this->setDatasetRel($rel);
		}
	}
	
	public function setDatasetRel(string $additionalRel){

		//check if we are overlapping???
		// $shouldBeAdded = false;
		// foreach($this->additionalRels as $rel){
		// 	if(stripos(haystack, needle)){

		// 	}
		// }
		$this->additionalRels[] = $additionalRel;
	}

	public function getDataset($uniqueCompanies=true, $enrich=true){

		$dataset = $this->repo->get($this->eventId, "exhibitor", $this->additionalRels);

		$res = $uniqueCompanies ? $dataset->unique("company_id")->values() : $dataset;

		CompanyData::setEventId($this->getEventId());

		return $enrich ? $res->mapInto(CompanyData::class) : $res;

	}

	public function getAllReps($enrich=true){

		$dataset = $this->repo->get($this->eventId, "representative", $this->additionalRepRels);

		CompanyData::setEventId($this->getEventId());

		return $enrich ? $dataset->mapInto(CompanyData::class) : $dataset;

	}

}