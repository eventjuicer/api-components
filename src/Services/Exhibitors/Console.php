<?php
namespace Eventjuicer\Services\Exhibitors;

use Eventjuicer\Services\Resolver;
use Eventjuicer\Services\GetByRole;
use Eventjuicer\Services\Exhibitors\CompanyData;
use Eventjuicer\Services\Revivers\ParticipantSendable;
use Illuminate\Support\MessageBag;
use Eventjuicer\Services\PartnerPerformance;

class Console {

	protected $eventId = 0;
	protected $groupId = 0;
	protected $repo;
	protected $dataset = [];
	protected $sendable;
	protected $params, $requirements = [];
	protected $messagebag;
	protected $performance;
	protected $additionalRels = ["fields", "company.data", "company.participants"];
	protected $additionalRepRels = ["fields", "company.data"];

	function __construct(
		GetByRole $repo, 
		ParticipantSendable $sendable,
		MessageBag $messagebag,
		PartnerPerformance $performance
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

		return $this->validateParams();
		//validate!
	}

	public function validateParams(){

	}

	public function getApi($resource = ""){

        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );  

        //TODO - map group_id to host!

    	$json = json_decode(file_get_contents("https://api.eventjuicer.com/v1/public/hosts/ecommerceberlin.com/".trim($resource, " /"), false, stream_context_create($arrContextOptions)), true);

    	if(empty($json) || empty($json["data"])) {
        	return ["data" => []];
    	}

    	return $json;

     
	}

	public function getTranslations(){

		if($this->getGroupId()>1){
			//expojuicer
			return json_decode(file_get_contents("https://localise.biz/api/export/all.json?format=multi&key=odUK6fy66gMjMpjQ7_IauP-JpjRpi3Nt"), true);
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

	public function getSendable($uniqueCompanies=true){

        $filtered = $this->sendable->filter($this->getDataset($uniqueCompanies), $this->getEventId());

        return $filtered;

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