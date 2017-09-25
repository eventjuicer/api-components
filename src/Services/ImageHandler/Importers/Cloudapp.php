<?php

namespace Eventjuicer\Services\ImageHandler\Importers;


class Cloudapp {



	function __construct()
	{

	}


	function parse()
	{


	$client = new GuzzleHttp\Client();
	$res = $client->request('GET', 'https://api.github.com/user', [
	'auth' => ['user', 'pass']
	]);
	echo $res->getStatusCode();
	// "200"
	echo $res->getHeader('content-type');
	// 'application/json; charset=utf8'
	echo $res->getBody();
	// {"type":"User"...'


		$data =	json_decode(fetch_external_data($url), true);					
		if(isset($data["remote_url"]))
		{				
			$body = str_replace($url, $data["remote_url"], $body);	
			$this->cache_image_file($data["remote_url"]);	
		}	


	}


}

