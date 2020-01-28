<?php

namespace Eventjuicer\Services;

use Illuminate\Support\Collection;

use Eventjuicer\Contracts\Email\Templated;

use SparkPost\SparkPost as SparkPostLib;

use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Illuminate\Http\Request;
use Eventjuicer\Repositories\ParticipantDeliveryRepository;


class SparkPost implements Templated {

	
	protected $sparky, $deliveries;

	protected $defaultParams = [

		"template_id" => "pl-visitors-registration",
		 
	];

	function __construct(Request $request, ParticipantDeliveryRepository $deliveries)
	{

		$this->deliveries = $deliveries;

		$httpClient = new GuzzleAdapter(new Client());
		
		$this->sparky = new SparkPostLib($httpClient, [
			'key'=> env("SPARKPOST_SECRET"), 
			'debug' => true,
			'async' => true
		]);
	}

	public function preview($templateId, array $substitution_data = [])
	{
			$promise = $this->sparky->request('POST', 
				'templates/'.$templateId.'/preview?draft=true', 
				[
					"substitution_data" => $substitution_data
				]);
				
				try {

					$response = $promise->wait();

					return $response->getBody();

				} catch (\Exception $e) {

					//dd($e->getMessage());

					//echo $e->getCode()."\n";

					return false;
				}
	}

	protected function substitutionData($str)
	{
		preg_match_all("@{{(?P<full>(?P<name>[a-zA-Z0-9_\-]+)(\?(?P<options>[a-z0-9=_\-&;]+)|))}}@i", $str, $matches);

		return !empty($matches["name"]) ? $matches["name"] : [];
	}


	public function bulk(Collection $recipients, array $data, $eventId = 0)
	{

		//check if we have legacy placeholders

		$substitutionData = $this->substitutionData($data["message"] . $data["subject"]);
 
		$mappedRecipients = $recipients->map(function($item) use ($substitutionData) {

				$subdata = $item->filter($substitutionData);
	
				return [
					"address" => [
						"name" => $item->translate("[[fname]] [[lname]]"),
						"email" => $item->email
					],
					"substitution_data" => $subdata,
					"metadata" => [
						"id" => $item->id,
						"company_id" => $item->company_id
					]
				];

		});

	


		$sparkData = [

			"options" => [
				"open_tracking" => false,
				"click_tracking" => false,
				"transactional" => false,
				//"sandbox" => false,
				//"inline_css" => false
			], 
   
			'content' => [
			 'from' => [
			     'name'  => array_get($data, "sender.name"),
			     'email' => array_get($data, "sender.email")
			 ],

			'subject' 	=> $data["subject"],
			//'html' 		=> $data["message"],

			'text' => $data["message"],


			//'template_id' => array_get($params, "template_id"),
			//'use_draft_template'=> true,

			],

			//'substitution_data' => [],

			'recipients' => $mappedRecipients->all(), 

		];




		if( $mappedRecipients->count() < 300 && isset($data["cc"]) && filter_var($data["cc"], FILTER_VALIDATE_EMAIL)  ){


			if(!isset($sparkData["content"]["headers"])){
				$sparkData["content"]["headers"] = array();
			}

			$sparkData["content"]["headers"]["CC"] = $data["cc"];

			$mappedCCRecipients = $mappedRecipients->map(function($item) use ($data) {

      			$item["address"]["header_to"] = $item["address"]["email"];
				$item["address"]["email"] = $data["cc"];
				unset($item["address"]["name"]);

				return $item;
			});

			$sparkData["recipients"] = array_merge( $sparkData["recipients"], $mappedCCRecipients->all() );

		}

		$promise = $this->sparky->transmissions->post($sparkData);

		try {

			
			$response = $promise->wait();


			if($eventId > 0 && !env("MAIL_TEST", true) )
	        {

	        	foreach($recipients AS $recipient){
	        		$this->deliveries->updateAfterSend($recipient->email, $eventId);
	        	}
        	}
			
			return [

				"code" 		=> $response->getStatusCode(),
				"body"		=> $response->getBody(),

			];

		} catch (\Exception $e) {
			
			return [

				"code" 		=> $e->getCode(),
				"message" 	=> $e->getMessage(),
				"body"		=> $e->getBody(),
				"request"	=> $e->getRequest()

			];

		}


	}

	public function send(array $params = [], $eventId = 0)
	{

		$params = array_merge($this->defaultParams, $params);

		$sparkData = [
			'content' => [

			// 'from' => [
			//     'name' => 'Targi eHandlu',
			//     'email' => array_get($params, "from")
			// ],
		//	'subject' => array_get($params, "subject"),

			// 'html' => '<html><body><h1>Congratulations, {{name}}!</h1><p>You just sent your very first mailing!</p></body></html>',
			// 'text' => 'Congratulations, {{name}}!! You just sent your very first mailing!',
			// ],

			'template_id' => array_get($params, "template_id"),

			'use_draft_template'=> true,

			],

			'substitution_data' => array_get($params, "substitution_data", []),

			'recipients' => [
				[
				    'address' => [
				        'name' => array_get($params, "recipient.name", ""),
				        'email' => array_get($params, "recipient.email", ""),
				    ],
				    // 'substitution_data' => [
				    // 	'token' => 'pru'
				    // ],
				],
			],
		];

		$sparkData = $this->addCCBCC($params, $sparkData);


		$promise = $this->sparky->transmissions->post($sparkData);

		try {
			

			$response = $promise->wait();


			if($eventId > 0 && !env("MAIL_TEST", true) )
	        {
	        	$this->deliveries->updateAfterSend(array_get($params, "recipient.email", ""), $eventId);
        	}

			return $response->getStatusCode();
			
			//$response->getBody();
		} catch (\Exception $e) {
			
			return [

				"code" 		=> $e->getCode(),
				"message" 	=> $e->getMessage(),
				"body"		=> $e->getBody(),
				"request"	=> $e->getRequest()

			];
		}



	}


	protected function addCCBCC(array $params, array $data){

		if(count($data["recipients"]) > 1 || !array_get( $data["recipients"][0], "address.email", false) ){
			return $data;
		}

		if( isset($params["cc"]) && filter_var($params["cc"], FILTER_VALIDATE_EMAIL) ){

			$data["content"]["headers"]["CC"] = $params["cc"];

			$data["recipients"][] = [
				"address" => [
					"header_to" => 	$data["recipients"][0]["address"]["email"],
					"email" => $params["cc"]
				]
			];

		}

		if( isset($params["bcc"]) && filter_var($params["bcc"], FILTER_VALIDATE_EMAIL) ){

			$data["content"]["headers"]["BCC"] = $params["bcc"];

			$data["recipients"][] = [
				"address" => [
					"header_to" => 	$data["recipients"][0]["address"]["email"],
					"email" => $params["bcc"]
				]
			];
 
		}

		return $data;
	}

}