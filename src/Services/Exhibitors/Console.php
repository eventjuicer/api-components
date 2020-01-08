<?php
namespace Eventjuicer\Services\Exhibitors;

use Eventjuicer\Services\Resolver;
use Eventjuicer\Services\GetByRole;
use Eventjuicer\Services\Exhibitors\CompanyData;
use Eventjuicer\Services\Revivers\ParticipantSendable;
use Illuminate\Support\MessageBag;

class Console {

	protected $eventId = 0;
	protected $repo;
	protected $dataset = [];
	protected $sendable;
	protected $params, $requirements = [];
	protected $messagebag;

	function __construct(
		GetByRole $repo, 
		ParticipantSendable $sendable,
		MessageBag $messagebag
	){

		$this->repo = $repo;
		$this->sendable = $sendable;
	 	$this->messagebag = $messagebag;

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

	public function run(string $domain, array $rels = ["fields", "company.data", "company.participants"]){

        $route = new Resolver( $domain );

        $this->eventId =  $route->getEventId();

        $this->dataset = $this->repo->get($this->eventId, "exhibitor", $rels);

	}

	public function getEventId(){

		return $this->eventId;
	}

	public function getSendable(){

        $filtered = $this->sendable->filter($this->getDataset(), $this->getEventId());

        return $filtered;

	}

	public function withCompanies(){
		return $this->getDataset()->unique("company_id");
	}

	public function getDataset($enrich=true){

		CompanyData::setEventId($this->getEventId());

		return $enrich ? $this->dataset->mapInto(CompanyData::class) : $this->dataset;

	}

}