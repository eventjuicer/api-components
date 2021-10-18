<?php 

namespace Eventjuicer\Services\Revivers;


use Carbon\Carbon;
use Illuminate\Support\Collection;


use Eventjuicer\Repositories\ParticipantDeliveryRepository;
use Eventjuicer\Repositories\ParticipantMuteRepository;

use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Eventjuicer\Repositories\Criteria\ColumnGreaterThan;

use Eventjuicer\ValueObjects\EmailAddress;
use Closure;

class ParticipantSendable {
	

	protected $deliveries;
	protected $mutes;

	protected $checkUniqueness = true;
	protected $checkDeliveries = true;
	protected $checkMutes = true;

	protected $validateEmails = false;

	protected $unique = [];
	protected $then = "";

	protected $resolver;
	protected $actions = [];
	protected $file = "";

	function __construct(ParticipantDeliveryRepository $deliveries, ParticipantMuteRepository $mutes)
	{
		$this->deliveries 	= $deliveries;
		$this->mutes 		= $mutes;
		
		$this->resolver = function($item){return $item->email; };

		$this->setMuteTime();

	}

	public function excludeFromFile(string $file){

		if(file_exists($file)){
			$this->file = strtolower( trim( file_get_contents($file) ) );
		}

	}

	public function setMuteTime($muteTime = 120)
	{
		$this->then 	= Carbon::now("UTC")->subMinutes( (int) $muteTime);
	}

	public function checkUniqueness(bool $val)
	{
		$this->checkUniqueness = $val;
	}

	public function checkDeliveries(bool $val)
	{
		$this->checkDeliveries = $val;
	}

	public function checkMutes(bool $val)
	{
		$this->checkMutes = $val;
	}

	public function howManyMuted(){

		$mutes =  $this->getMutes();

		return $mutes ? count($mutes) : 0;
	}

	public function validateEmails(bool $val)
	{
		$this->validateEmails = $val;
	}

	public function setEmailResolver(Closure $func)
	{
		$this->resolver = $func;
	}
	
	public function filter(Collection $dataset, $eventId = 0, array $excludes = [])
	{

		$deliveries = $this->getDeliveries($eventId);
		$mutes 		= $this->getMutes($eventId);

		$filtered = $dataset->filter(function($item) use ($deliveries, $mutes, $excludes) 
		{ 

			$email = $this->resolver->__invoke($item) ;

			//normalize

			$email = trim( strtolower($email) );

			if($this->checkUniqueness && in_array($email, $this->unique))
			{
				return false;
			}

			if(!empty($excludes) && in_array($email, $excludes) )
			{
				return false;
			}

			if(!empty($this->file) && strpos($this->file, $email)!==false ){
				return false;
			}

			if($this->checkDeliveries && in_array($email, $deliveries) )
			{
				return false;
			}

			if($this->checkMutes && in_array($email, $mutes) )
			{
				return false;
			}

			if($this->validateEmails && ! (new EmailAddress($email))->isValid() ) {
				return false;
			}		

			$this->unique[] = $email;

			return true;
		});
 

		return $filtered;

	}

	


	protected function getDeliveries($eventId = 0)
	{
		if($eventId)
		{
			$this->deliveries->pushCriteria( new BelongsToEvent($eventId) );
		}
		
        $this->deliveries->pushCriteria( new ColumnGreaterThan("created_at", $this->then) );

		return $this->deliveries->all()->pluck("email")->all();	
	}

	protected function getMutes($eventId = 0)
	{
		if($eventId)
		{
	//		temporary skip all mutes!!!
	//		$this->mutes->pushCriteria( new BelongsToEvent($eventId) );
		}

		return $this->mutes->all()->pluck("email")->all();	
	}

}