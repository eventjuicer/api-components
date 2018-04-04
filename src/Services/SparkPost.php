<?php

namespace Eventjuicer\Services;

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
			'async' => true ]);
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


	public function send(array $params = [])
	{

		$params = array_merge($this->defaultParams, $params);


		$promise = $this->sparky->transmissions->post([
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
			// 'cc' => [
			// [
			//     'address' => [
			//         'name' => 'ANOTHER_NAME',
			//         'email' => 'ANOTHER_EMAIL',
			//     ],
			// ],
			// ],
			// 'bcc' => [
			// [
			//     'address' => [
			//         'name' => 'AND_ANOTHER_NAME',
			//         'email' => 'AND_ANOTHER_EMAIL',
			//     ],
			// ],
			// ],
		]);

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