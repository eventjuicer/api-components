<?php

namespace Eventjuicer;

use Firebase\JWT\JWT;

// Requires: composer require firebase/php-jwt

//https://firebase.google.com/docs/auth/admin/verify-id-tokens

use Illuminate\Database\Eloquent\Model;

class Firebase {
	
	protected $config;

	function __construct()
	{


		$this->config = json_decode(fixLocalFileJson(file_get_contents(app()->basePath(".firebase"))));

		if(is_null($this->config))
		{
			throw new \Exception("Error when reading firebase config. " . json_last_error_msg());
		}

		if(!isset($this->config->client_email) OR !isset($this->config->private_key))
		{
			throw new \Exception("Missing config data in .firebase file...");
		}
	
	}


	function create_custom_token(Model $model, array $claims = [])
	{

	  $uid = $model->id;

	  $name = strtolower( (new \ReflectionClass($model))->getShortName() );

	  $now_seconds = time();

	  $payload = array(
	    "iss" => $this->config->client_email,
	    "sub" => $this->config->client_email,
	    "aud" => "https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit",
	    "iat" => $now_seconds,
	    "exp" => $now_seconds+(60*60),  // Maximum expiration time is one hour
	    "uid" => $name ."-". $uid,
	    "claims" => $claims
	  );
	  
	  return JWT::encode($payload, $this->config->private_key, "RS256");
	}



}