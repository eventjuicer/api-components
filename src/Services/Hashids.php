<?php namespace Eventjuicer\Services;

use Hashids\Hashids as _Hashids;

class Hashids {
	
	protected $key;
	protected $length = 4;
	protected $dictionary = "abcdefghijkmnpqrstuvwxyz";

	protected $hashids;

	function __construct($skipLimitations = false)
	{
		$this->key = trim(env("HASHIDS_KEY", false));

		if(!$this->key)
		{
			throw new \Exception("No HASHIDS_KEY in .env!");
		}

		if($skipLimitations)
		{
			$this->hashids = new _Hashids(
	        	$this->key
	    	);
		}
		else
		{
			$this->hashids = new _Hashids(
	        	$this->key, 
	        	$this->length, 
	        	$this->dictionary
	    	);
		}

		

	}

	function encodeArr(array $ids)
	{
	       
	        return $this->hashids->encode($ids);
	}

	function encode($id = 0)
	{
	       
	        return $this->hashids->encode($id);
	}

	function decode($code = "")
	{      

	        $arr = $this->hashids->decode($code);

	        if(count($arr) === 1)
	        {
	        	return $arr[0];
	        }

	        return $arr;
	}



}