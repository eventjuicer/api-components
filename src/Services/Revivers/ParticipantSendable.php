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

	protected $unique = [];
	protected $then = "";

	protected $resolver;

	function __construct(ParticipantDeliveryRepository $deliveries, ParticipantMuteRepository $mutes)
	{
		$this->deliveries 	= $deliveries;
		$this->mutes 		= $mutes;
		
		$this->resolver = function($item){return $item->email; };

		$this->setMuteTime();
	}

	public function setMuteTime($muteTime = 10)
	{
		$this->then 	= Carbon::now("UTC")->subHours( (int) $muteTime);
	}


	public function filter(Collection $dataset, $eventId = 0, array $excludes = [])
	{

		$deliveries = $this->getDeliveries($eventId);
		$mutes 		= $this->getMutes($eventId);

		$filtered = $dataset->filter(function($item) use ($deliveries, $mutes, $excludes) 
		{ 

			$email = $this->resolver->__invoke($item);

			if(
				in_array($email, $this->unique) || 
				in_array($email, $deliveries) || 
				in_array($email, $mutes)
			)
			{
				return false;
			}

			if(!empty($excludes) && in_array($email, $excludes))
			{
				return false;
			}

			$this->unique[] = $email;

			return true;
		});
 

		return $filtered;

	}

	protected function setEmailResolver(Closure $func)
	{
		$this->resolver = $func;
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
			$this->mutes->pushCriteria( new BelongsToEvent($eventId) );
		}

		return $this->mutes->all()->pluck("email")->all();	
	}

}