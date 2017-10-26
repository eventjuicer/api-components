<?php namespace Eventjuicer\Services;

use Hashids\Hashids as _Hashids;

class Hashids {
	
	protected $key;
	protected $length = 4;
	protected $dictionary = "abcdefghijkmnpqrstuvwxyz";

	function __construct()
	{
		$this->key = trim(env("HASHIDS_KEY", false));

		if(!$this->key)
		{
			throw new \Exception("No HASHIDS_KEY in .env!");
		}

	}

	function encode($id = 0)
	{
	        $hashids = new _Hashids(
	        	$this->key, 
	        	$this->length, 
	        	$this->dictionary
	        );
	        return $hashids->encode($id);
	}

	function decode($code = "", $flat = true)
	{      
	        $hashids = new _Hashids(
	        	$this->key, 
	        	$this->length, 
	        	$this->dictionary
	        );

	        $id = $hashids->decode($code);
	        return !empty($id[0]) ? $id[0] : 0;
	}



}