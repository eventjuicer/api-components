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

	public function getSendable($uniqueCompanies=true){

        $filtered = $this->sendable->filter($this->getDataset($uniqueCompanies), $this->getEventId());

        return $filtered;

	}

	public function getDataset($uniqueCompanies=true, $enrich=true){

		$res = $uniqueCompanies ? $this->dataset->unique("company_id") : $this->dataset;

		CompanyData::setEventId($this->getEventId());

		return $enrich ? $res->mapInto(CompanyData::class) : $res;

	}

}