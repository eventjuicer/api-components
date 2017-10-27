<?php namespace Eventjuicer\Services;

use GuzzleHttp\Client as Guzzle;

class ImageEncode {
	
	protected $url;
	protected $file;
	protected $type;


	function __construct($url)
	{
		$this->url = $url;

		if(strpos($this->url, "http")===false)
		{
			throw new \Exception("Bad image URL...");
		}

		$request = (new Guzzle())->request("GET", $this->url);

		$this->file = (string) $request->getBody();

		$this->type = finfo_buffer(finfo_open(), $this->file, FILEINFO_MIME_TYPE);

		if(strpos($this->type, "image/")===false)
		{
			throw new \Exception("This file is not an image!!!!");
		}

	}

	function encode()
	{

		return "data:".$this->type.";base64," . base64_encode($this->file);
	}

	function __toString()
	{
		return (string) $this->encode();
	}

}
