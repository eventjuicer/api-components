<?php

namespace Eventjuicer\Services;

use Eventjuicer\Contracts\Email\Templated;

use SparkPost\SparkPost as SparkPostLib;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Illuminate\Http\Request;


class SparkPost implements Templated {

	
	protected $sparky;

	function __construct(Request $request)
	{
		$httpClient = new GuzzleAdapter(new Client());
		
		$this->sparky = new SparkPostLib($httpClient, [
			'key'=> env("SPARKPOST_SECRET"), 
			'async' => true ]);
	}


	public function send()
	{

		$promise = $this->sparky->transmissions->post([
			'content' => [
			'from' => [
			    'name' => 'Targi eHandlu',
			    'email' => 'expojuicer@expojuicer.com',
			],
			// 'subject' => 'First Mailing From PHP',
			// 'html' => '<html><body><h1>Congratulations, {{name}}!</h1><p>You just sent your very first mailing!</p></body></html>',
			// 'text' => 'Congratulations, {{name}}!! You just sent your very first mailing!',
			// ],

			'template_id' => "exhibitordeck-auth-request-an-access",
			'use_draft_template'=> true,

			],

			'substitution_data' => [
				'name' => 'YOUR_FIRST_NAME'
			],
			'recipients' => [
				[
				    'address' => [
				        'name' => 'Adam Zygadlewicz',
				        'email' => 'adam@zygadlewicz.com',
				    ],
				    'substitution_data' => [
				    	'token' => 'pru'
				    ],
				],
				// [
				//     'address' => [
				//         'name' => 'Adam Z Zygadlewicz',
				//         'email' => 'adam+test@zygadlewicz.com',
				//     ],
				//     'substitution_data' => [
				//     	'token' => 'sru'
				//     ],
				// ],

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