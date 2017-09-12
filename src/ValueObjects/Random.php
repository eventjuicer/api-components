<?php 

namespace ValueObjects;

class Random {


	protected $hash;
	
	function __construct()
	{
		$this->hash = hash("sha1", microtime(true) . mt_rand(1, mt_getrandmax()));	
	}


	function __toString()
	{
		return (string) $this->hash;
	}




}