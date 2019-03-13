<?php

namespace Eventjuicer\Services;

use Illuminate\Support\Collection;

use Eventjuicer\Contracts\Email\Templated;

use SparkPost\SparkPost as SparkPostLib;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Illuminate\Http\Request;

class SparkPost implements Templated {

	
	protected $sparky;

	protected $defaultParams = [

		"template_id" => "pl-visitors-registration",
		 
	];

	function __construct(Request $request)
	{
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

		return $matches["name"];
	}


	public function bulk(Collection $recipients, array $data)
	{

		//check if we have legacy placeholders

		$substitutionData = $this->substitutionData($data["message"] . $data["subject"]);
 
		$recipients = $recipients->map(function($item) use ($substitutionData) {

				$subdata = $item->filter($substitutionData);
	
				return [
					"address" => [
						"name" => $item->translate("[[fname]] [[lname]]"),
						"email" => $item->email
					],
					"substitution_data" => $subdata,
					"metadata" => [
						"id" => $item->id
					]
				];

		})->all();

		try {

			$promise = $this->sparky->transmissions->post([

			"options" => [
				"open_tracking" => false,
				"click_tracking" => false,
				"transactional" => false,
				// "sandbox" => false,
				// "inline_css" => false
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

			'recipients' => $recipients, 

			]);
			
			$response = $promise->wait();
			
			return [

				"code" 		=> $response->getCode(),
				"message" 	=> $response->getMessage(),
				"body"		=> $response->getBody(),
				"request"	=> $response->getRequest()

			];

		} catch (\Exception $e) {
			//return $e->getCode();
			return $e->getCode() . $e->getMessage();
		}


	}

	public function send(array $params = [])
	{

		$params = array_merge($this->defaultParams, $params);

		$data = [
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

		if( isset($params["cc"]) && filter_var($params["cc"], FILTER_VALIDATE_EMAIL) ){

			if(!isset($data["cc"])){
				$data["cc"] = array();
			}

			$data["cc"][] = ["address" => [
				"name" => $params["cc"],
				"email" => $params["cc"]
			]];
 
		}

		if( isset($params["bcc"]) && filter_var($params["bcc"], FILTER_VALIDATE_EMAIL) ){

			if(!isset($data["bcc"])){
				$data["bcc"] = array();
			}

			$data["bcc"][] = ["address" => [
				"name" => $params["bcc"],
				"email" => $params["bcc"]
			]];
 
		}


		$promise = $this->sparky->transmissions->post($data);

		try {
			$response = $promise->wait();
			return $response->getStatusCode();
			//$response->getBody();
		} catch (\Exception $e) {
			return $e->getCode();
			//$e->getMessage();
		}



	}

}