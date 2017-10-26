<?php namespace Eventjuicer\Services;

class ImageEncode {
	
	protected $url;

	function __construct($url)
	{
		$this->url = $url;
	}

	function encode()
	{

		

	}

// <img src="data:image/png;base64,{{base64_encode(file_get_contents(array_get($participant, "fields.avatar") ))}}" alt="">



	function __toString()
	{

	}

}