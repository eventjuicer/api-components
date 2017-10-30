<?php namespace Eventjuicer\Services;

use GuzzleHttp\Client as Guzzle;

class ImageShared {
	

	protected $urlOrPath;
	protected $file;
	protected $type;


	function __construct($urlOrPath)
	{

		$this->urlOrPath = trim($urlOrPath);
		
		if(strpos($this->urlOrPath, "http")===0)
		{
			
			$this->file = $this->getExternalImage($this->urlOrPath);

		}
		else if(file_exists($this->urlOrPath))
		{
			$this->file = file_get_contents($this->urlOrPath);
		}
		else
		{
			throw new \Exception("Bad image source...");
		}

	

		$this->checkFileType();

	}


	protected function getExternalImage(string $urlOrPath)
	{

		$request = (new Guzzle())->request("GET", $urlOrPath);

		return (string) $request->getBody();
	}

	protected function checkFileType()
	{

		$this->type = finfo_buffer(finfo_open(), $this->file, FILEINFO_MIME_TYPE);

		if(strpos($this->type, "image/")===false)
		{
			throw new \Exception("This file is not an image!!!!");
		}
	}


	public function getImage()
	{

		return $this->file;
	}


}