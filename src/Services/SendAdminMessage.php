<?php namespace Eventjuicer\Services;



use Eventjuicer\Services\Personalizer;
use Eventjuicer\Services\Revivers\ParticipantSendable;


use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\Criteria\WhereIn;

use Illuminate\Support\Collection;



/**
*

array ( 
    'ids' => array ( 0 => '50060', ), 
    'subject' => 'Targi eHandlu - wiadomość od organizatora', 
    'sender' => array ( 
        'email' => 'targiehandlu@targiehandlu.pl', 
        'name' => 'asdasd', ), 
    'message' => 'sadasdasd asdasdasdas asdasd asdasdasd sadasdasd asdasdasdas asdasd asdasdasd', 
    'appendlink' => 1, 
    'event_id' => 76, 
    'accesslink' => array ( 
        'name' => 'oto link dostępu do Twojego profilu (nie udostępniaj go osobom nieupoważnionym):', 
        'href' => 'https://account.targiehandlu.pl/#/login?token=[[token]]', ), 
    'unique' => '1', 

)

***/


class SendAdminMessage {

	protected $participants;
	protected $sendable;

	function __construct(ParticipantRepository $participants, ParticipantSendable $sendable)
	{

		$this->participants = $participants;
		$this->sendable 	= $sendable;

		$this->sendable->validateEmails(true);

	}

	public function validate(array $data)
	{
		$errors = [];

		if(empty($data["ids"]))
        {
            $errors[] = "ids.empty";
        }

        if(empty($data["message"]) || strlen($data["message"]) < 10)
        {
        	$errors[] = "message.too_short";
        }

        if(empty($data["subject"]) || strlen($data["subject"]) < 5)
        {
        	$errors[] = "subject.too_short";
        }

        if(empty($data["sender"]) || empty($data["sender"]["email"]))
        {
            $errors[] = "sender.empty";
        }

        if(!empty($data["appendlink"]) && (empty($data["accesslink"]) || empty($data["accesslink"]["href"]) ))
        {
            $errors[] = "sender.empty";
        }

        return $errors;
	}


	public function legacy(array $data)
	{
		$data["message"] = str_replace(["[[", "]]"], ["{{", "}}"], array_get($data, "message", ""));
		$data["subject"] = str_replace(["[[", "]]"], ["{{", "}}"], array_get($data, "subject", ""));

		if(isset($data["accesslink"]))
		{
			$data["accesslink"]["href"] = str_replace(["[[", "]]"], ["{{", "}}"], array_get($data, "accesslink.href", ""));
		}

		if(!empty($data["appendlink"]))
		{
			$data["message"] = $data["message"] . PHP_EOL . $data["accesslink"]["href"];
		}

		return $data;
	}


	public function make(array $ids, $eventId, bool $uniqueCheck = true, bool $throttle = false)
	{
		$this->participants->pushCriteria( new WhereIn("id", $ids) );
		$this->participants->with(["fields", "ticketpivot"]);
		$all = $this->participants->all();

		//COMPAT is_alive()... check for any SOLD=1

		$all =  $all->filter(function($model) {

			return ( $model->ticketpivot->where("sold", 1)->count() > 0 ) ? true : false;
		});

		// $this->sendable->checkUniqueness( $uniqueCheck);
	
		// //check if we do not spam too much....

		// if($throttle) {
		// 	$all = $this->sendable->filter($all, $eventId)->values();
		// }

		return $all->mapInto(Personalizer::class);

	}

	public function personalize()
	{

	}	

}